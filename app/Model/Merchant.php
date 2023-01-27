<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Utils\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $password
 * @property string $salt
 * @property string $merchant_code
 * @property string $merchant_name
 * @property string $payment_secret
 * @property string $phone
 * @property string $mail
 * @property int $agent_id
 * @property string $agent_name
 * @property int $state
 * @property string last_ip
 * @property string allow_ip
 * @property int $last_login
 * @property int $login_count
 * @property int $created
 * @property int $updated
 * @property int $merchant_type
 * @property int $pay_in
 * @property int $pay_out
 * @property int $sort
 * @property string $remark
 * @property int $google_verify
 * @property string $current_ip
 */
class Merchant extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    const SECRET_KEY = '1pI&)12h5Ldf0921#_)!~SAK395sxv>?';
    /**
     * 状态类型
     */
    const STATUS_TYPES = [0 => '禁用', 1 => '正常'];
    /**
     * 商户类型
     */
    const MERCHANT_TYPES = [1 => '直属商户', 2 => '非直属商户'];
    protected ?string $table = 'merchants';
    protected ?string $dateFormat = 'U';

    // 判断状态

    /**
     * @return array
     */
    public static function getRelatedAll(bool $all = true): array
    {
        $result = [];
        $rows = self::all();
        foreach ($rows as $r) {
            if ((!$all && $r->state == 1) || $all) {
                $result[$r->id] = $r->merchant_name;
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function beforeSave(array &$data): bool
    {
        if (isset($data['re_password'])) {
            // 如果设置密码
            unset($data['re_password']);
        }
        if (isset($data['password'])) {
            // 如果设置了密码, 则生成密码
            $data['secret'] = Str::random(32);
            $data['password'] = self::getPassword(trim($data['password']), $data['secret']);
        }
        if (!isset($data['merchant_code'])) {
            $data['merchant_code'] = 'MER' . date('YmdHis') . mt_rand(10, 99);
        }
        return true;
    }

    /**
     * @param string $realPassword
     * @param string $secret
     * @return string
     */
    public static function getPassword(string $realPassword, string $secret): string
    {
        return md5(self::SECRET_KEY . '-' . $secret . '-' . md5($realPassword));
    }

    /**
     * 创建之后
     * @param array $data
     */
    public static function afterCreate(array $data)
    {
        $currentTime = time();
        MerchantAccount::query()->insert([
            'merchant_id' => $data['id'],
            'merchant_name' => $data['name'],
            'state' => 1,
            'created' => $currentTime,
            'updated' => $currentTime,
        ]);
    }

    public function isEnable(): bool
    {
        return $this->state == 1;
    }
}
