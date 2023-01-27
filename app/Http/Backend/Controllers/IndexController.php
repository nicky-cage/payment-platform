<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Common\{GoogleCode, Utils};
use App\Model\{Admin, AdminLogin, AdminLoginLog};
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Annotation\{Controller, GetMapping, PostMapping, RequestMapping};
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: '/backend/index')]
class IndexController extends BaseController
{
    /**
     * 最大允许错误登录次数
     */
    const MAX_LOGIN_ERROR_COUNT = 100;

    /**
     * 测试
     * @param RequestInterface $request
     * @return array
     */
    #[GetMapping(path: 'test')]
    public function test(RequestInterface $request): array
    {
        $user = $request->input('user', 'Hyperf');
        $method = $request->getMethod();
        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }

    #[GetMapping(path: 'error')]
    public function showError(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        $message = $request->getQueryParams()['message'] ?? '';
        return self::error($request, $render, $message);
    }

    /**
     * 后台默认首页
     * @param RequestInterface $request
     * @param RenderInterface $render
     * @return ResponseInterface
     */
    #[GetMapping(path: 'index')]
    public function index(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        return self::render($request, $render, [], 'index/index');
    }

    /**
     * 用户登录
     * @param RequestInterface $request
     * @param SessionInterface $session
     * @return array
     */
    #[PostMapping(path: 'login')]
    public function login(RequestInterface $request, SessionInterface $session): array
    {
        $requestIp = $request->getServerParams()['remote_addr'] ?? '127.0.1.1';
        $isMaxErrorCount = function (bool $add = true) use ($requestIp): bool { // 是否达到错误上限
            $cacheKey = 'login_error:' . $requestIp;
            $errorCount = $this->redis->get($cacheKey); // {'error_count': 1, 'error_time': 0}
            if (!$errorCount) { // 如果没
                $this->redis->set($cacheKey, 1, 3600);
                return false;
            }
            if ($errorCount >= self::MAX_LOGIN_ERROR_COUNT) {
                return true;
            }
            if ($add) {
                $this->redis->set($cacheKey, $errorCount + 1, 3600);
            }
            return false;
        };
        $delLoginError = function () use ($requestIp) { // 删除登录错误信息
            $cacheKey = 'login_error:' . $requestIp;
            $this->redis->del($cacheKey);
        };
        $checkMaxErrorCount = function (string $message = '用户登录错误达到上限') use ($isMaxErrorCount): array {
            return self::jsonErr($message);
        };
        if ($isMaxErrorCount()) {
            return self::jsonErr('你的密码输入错误次数过多,请稍后再试');
        }

        $postedData = $request->all();
        if (!isset($postedData['username']) || !isset($postedData['password']) || !isset($postedData['verify_code'])) {
            return $checkMaxErrorCount('缺少用户名称或密码、验证码');
        }
        $userName = trim($postedData['username']); // 用户名称
        $password = trim($postedData['password']); // 密码
        $verifyCode = trim($postedData['verify_code']); // 验证码
        $passLen = strlen($password);
        if (!preg_match('/[a-zA-Z0-9_]{5,20}/i', $userName) || ($passLen < 6 || $passLen > 20)) {
            return $checkMaxErrorCount('用户名称或密码格式错误');
        }
        $sessionCode = $this->session->get('verify_code'); // 保存在session里的验证码
        if ($verifyCode != $sessionCode) { // 隐藏的验证码 - 月日时分 - 03060101
            return $checkMaxErrorCount('登录验证码校验错误');
        }
        $row = Admin::query()->where('name', $userName)->first();
        if (!$row) {
            return $checkMaxErrorCount('用户名称或密码有误');
        }

        // 检测ip
        $ipArr = array_map(function ($v) {
            return trim($v);
        }, explode(',', $row->allow_ips));
        if (!in_array($requestIp, $ipArr)) {
            return $checkMaxErrorCount('此IP无登录权限');
        }

        // 检测google验证码
        $gCode = trim($postedData['google_code'] ?? '');
        if ($gCode == '') {
            return $checkMaxErrorCount('必须输入谷歌验证码');
        }
        if ($row->google_verify == 1 && !GoogleCode::verifyCode($row->google_secret, $gCode)) { // 如果启用了google验证码, 则
            return $checkMaxErrorCount('谷歌验证密码错误');
        }

        // 检测状态
        if ($row->state == 0) {
            return $checkMaxErrorCount('用户状态异常,无法登录');
        }
        $realPassword = Utils::getRealPassword($password, $row->salt);
        if ($realPassword != $row['password']) {
            return $checkMaxErrorCount('用户名称或者密码错误');
        }

        $saltNew = Utils::getSalt();
        $passNew = Utils::getRealPassword($password, $saltNew);
        $data = [
            'salt' => $saltNew,
            'password' => $passNew,
            'login_count' => ($row['login_count'] + 1),
            'last_login' => time(),
            'current_ip' => $requestIp,
        ];
        if ($row->google_verify == 0) { // 如果没有开启google验证, 则自动打开
            $data['google_verify'] = 1;
        }
        if (!Admin::query()->where('id', $row['id'])->update($data)) {
            return $checkMaxErrorCount('保存用户登录信息出错');
        }

        $delLoginError(); // 删除错误信息
        (new AdminLogin())->store([
            'id' => $row['id'],
            'name' => $row['name'],
            'login_count' => $row['login_count'],
            'last_login' => $row['last_login'],
            'last_ip' => $row['last_ip'],
            'current_ip' => $requestIp,
        ]);
        unset($row->salt);
        unset($row->password);
        $row->current_ip = $requestIp;
        $this->session->forget(['verify_code']);
        AdminLogin::set($this->session, $row);

        $headers = $request->getHeaders();
        AdminLoginLog::query()->insert([
            'admin_id' => $row->id,
            'admin_name' => $row->name,
            'login_ip' => $requestIp,
            'created' => time(),
            'user_agent' => $headers['user-agent'][0] ?? '',
            'domain' => $headers['host'][0] ?? '',
        ]);

        return self::jsonOK();
    }

    /**
     * 后台管理主界面
     * @param RequestInterface $request
     * @param RenderInterface $render
     * @return ResponseInterface
     */
    #[GetMapping(path: 'manage')]
    public function manage(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        $login = AdminLogin::get($this->session);
        return self::render($request, $render, [
            'adminName' => $login->name,
        ], 'index/manage');
    }

    #[GetMapping(path: 'profile')]
    public function profile(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        $login = AdminLogin::get($this->session);
        $admin = Admin::query()->where(['id' => $login->id])->first();
        return self::render($request, $render, [
            'id' => $admin->id,
            'info' => $admin,
            'action' => 'profile_save',
        ]);
    }

    #[PostMapping(path: 'profile_save')]
    public function profileSave(RequestInterface $request): array
    {
        $params = $request->all();
        Admin::query()->where(['id' => intval($params['id'])])->update([
            'nickname' => trim($params['nickname']),
            'mail' => trim($params['mail']),
            'updated' => time(),
        ]);
        return self::jsonOK();
    }

    #[GetMapping(path: 'password')]
    public function password(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        return self::render($request, $render, [
            'action' => 'password_save'
        ]);
    }

    #[PostMapping(path: 'password_save')]
    public function passwordSave(RequestInterface $request): array
    {
        $params = $request->all();
        $adminInfo = AdminLogin::get($this->session);
        if (strlen(trim($params['password'])) < 6 || strlen(trim($params['password'])) > 16) {
            return self::jsonErr("输入密码格式有误");
        }
        if (strlen(trim($params['password_new'])) < 6 || strlen(trim($params['password_new'])) > 16) {
            return self::jsonErr("输入密码格式有误");
        }
        if (strlen(trim($params['password_rep'])) < 6 || strlen(trim($params['password_rep'])) > 16) {
            return self::jsonErr("输入密码格式有误");
        }
        if (trim($params['password_new']) !== trim($params['password_rep'])) {
            return self::jsonErr("两次输入的密码不一致");
        }

        $gCode = trim($params['google_code'] ?? '');
        if ($gCode == '') {
            return self::jsonErr('输入谷歌验证密码格式有误');
        }
        if ($adminInfo->google_verify == 1 && !GoogleCode::verifyCode($gCode, $adminInfo->google_secret)) {
            return self::jsonErr('输入谷歌验证密码错误');
        }

        $admin = Admin::query()->where($adminInfo->id)->first();
        $password = Utils::getRealPassword(trim($params['password']), $admin->salt);
        if ($password !== $admin->password) {
            return self::jsonErr("旧的密码输入有误");
        }

        $admin->salt = Utils::getSalt();
        $admin->password = Utils::getRealPassword($password, $admin->salt);
        $admin->updated = time();
        if (!$admin->save()) {
            return self::jsonErr('保存密码信息失败');
        }
        return self::jsonOK();
    }

    /**
     * 默认后台右部
     * @param RequestInterface $request
     * @param RenderInterface $render
     * @return ResponseInterface
     */
    #[GetMapping(path: 'right')]
    public function right(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        return self::render($request, $render, [], 'index/right');
    }

    #[RequestMapping(path: 'logout', methods: 'get, post')]
    public function logout(): array
    {
        $this->session->clear();
        return self::jsonOk();
    }

    #[GetMapping(path: 'google')]
    public function google(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        $secret = GoogleCode::secret();
        return self::render($request, $render, [
            'action' => 'google_bind',
            'url' => GoogleCode::getPlatformQRCodeURL($secret),
            'secret' => $secret,
        ], 'index/google');
    }

    #[PostMapping(path: 'google_bind')]
    public function googleBind(RequestInterface $request): array
    {
        $secret = trim($request->all()['google_secret'] ?? '');
        if ($secret == '') {
            return self::jsonErr('缺少必要谷歌验证密码');
        }

        $login = (object)$this->session->get(AdminLogin::KEY);
        $admin = Admin::query()->where(['id' => $login->id])->first();
        if (!$admin) {
            return self::jsonErr('缺少登录用户信息');
        }

        $admin->updated = time();
        $admin->google_secret = $secret;
        if (!$admin->save()) {
            return self::jsonErr('保存谷歌密钥失败');
        }

        return self::jsonOk();
    }

    #[PostMapping(path: "google_test")]
    public function googleTest(RequestInterface $request): array
    {
        $value = trim($request->all()['value'] ?? '');
        if ($value == '') {
            return self::jsonErr('秘须输入谷歌验证密码');
        }
        if (!is_numeric($value)) {
            return self::jsonErr('格式有误');
        }

        $login = AdminLogin::get($this->session);
        $admin = Admin::query()->where(['id' => $login->id])->first();
        if (!$admin) {
            return self::jsonErr('缺少用户信息');
        }
        if (!GoogleCode::verifyCode($admin->google_secret, $value)) {
            return self::jsonErr('验证失败');
        }
        return self::jsonOk();
    }


    /**
     * 生成图形验证码
     * @return string
     */
    #[GetMapping(path: 'code')]
    public function verifyCode(): string
    {
        $strCode = '';
        $verifyCode = Utils::verifyCode($strCode);
        $this->session->set('verify_code', $strCode);
        return $verifyCode;
    }
}
