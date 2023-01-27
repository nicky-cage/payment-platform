<?php

declare(strict_types=1);

namespace App\Common;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;

class Utils
{
    /**
     * @var string
     */
    private static string $SECRET_KEY = 'A1@#(DsL)(!@#H985K2CBdh26(##a."|';

    /**
     * @return string
     */
    public static function getPassword(): string
    {
        return sprintf("%s%2d", strtoupper(Str::random(6)), mt_rand(10, 99));
    }

    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function getRealPassword(string $password, string $salt): string
    {
        return md5(self::$SECRET_KEY . $password . md5($salt));
    }

    /**
     * 生成密钥
     * @return string
     */
    public static function getSalt(): string
    {
        return Str::random(32);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    public static function clientIP(RequestInterface $request): string
    {
        $keys = [
            'x-forwarded-for',
            'http_x_forwarded_for',
            'x-real-ip',
        ];
        $headers = $request->getHeaders();
        foreach ($keys as $k) {
            if (isset($headers[$k])) {
                return explode(',', $headers[$k][0])[0];
            }
        }

        return $request->getServerParams()['remote_addr'] ?? '';
    }

    /**
     * 生成随机验证码
     * @param string $codeStr 用于对外输出验证码
     */
    public static function verifyCode(string &$codeStr = ''): string
    {
        $width = 128;
        $height = 26;
        $image = imagecreatetruecolor($width, $height);  //1>设置验证码图片大小的函数
        $bgcolor = imagecolorallocate($image, 255, 255, 255); //#ffffff //5>设置验证码颜色 imagecolorallocate(int im, int red, int green, int blue);
        imagefill($image, 0, 0, $bgcolor); //6>区域填充 int imagefill(int im, int x, int y, int col) (x,y) 所在的区域着色,col 表示欲涂上的颜色
        $captcha_code = ""; //10>设置变量
        for ($i = 0; $i < 5; $i++) { //7>生成随机的字母和数字
            $fontsize = 12; //设置字体大小
            $fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));   //0-120深颜色 //设置字体颜色，随机颜色
            $data = 'abcdefghigkmnpqrstuvwxy3456789'; //设置需要随机取的值,去掉容易出错的值如0和o
            $fontcontent = substr($data, rand(0, strlen($data)), 1); //取出值，字符串截取方法 strlen获取字符串长度
            $captcha_code .= $fontcontent; //10>.=连续定义变量
            $x = ($i * 100 / 4) + rand(5, 10); //设置坐标
            $y = rand(5, 10);
            imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
        }
        $codeStr = $captcha_code;
        //$_SESSION['authcode'] = $captcha_code; //10>存到session
        for ($i = 0; $i < 200; $i++) { //8>增加干扰元素，设置雪花点
            $pointcolor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200)); //设置点的颜色，50-200颜色比数字浅，不干扰阅读
            imagesetpixel($image, rand(1, $width - 1), rand(1, $height - 1), $pointcolor); //imagesetpixel — 画一个单一像素
        }
        for ($i = 0; $i < 4; $i++) { //9>增加干扰元素，设置横线
            $linecolor = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220)); //设置线的颜色
            imageline($image, rand(1, $width - 1), rand(1, $height - 1), rand(1, $width - 1), rand(1, $height - 1), $linecolor); //设置线，两点一线
        }

        ob_start();
        imagepng($image);
        imagedestroy($image);
        $bin = ob_get_clean();
        $base64 = base64_encode($bin);

        return 'data:image/png;base64,' . $base64;
    }

    /**
     * 生成订单号
     */
    public static function getOrderNumber(string $prefix): string
    {
        return strtoupper($prefix) . date('YmdHis') . mt_rand(1000, 9999);
    }

    /**
     * @param array $data
     * @param string $key 密钥
     * @return string
     */
    public static function getSign(array $data, string $key): string
    {
        ksort($data);
        $signStr = '';
        foreach ($data as $k => $v) {
            if ($k == 'sign' || trim(strval($v)) == '') {
                continue;
            }
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr .= 'key=' . $key;
        echo "签名:" . $signStr, "\n";
        return strtoupper(md5($signStr));
    }

    /**
     * @param string $words
     * @param int $length
     * @return string
     */
    public static function truncate(string $words, int $length = 32): string
    {
        $len = mb_strlen($words);
        if ($len > $length) {
            return mb_substr($words, 0, $length) . '...';
        }

        return $words;
    }
}
