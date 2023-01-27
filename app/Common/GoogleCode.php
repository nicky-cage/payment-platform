<?php

declare(strict_types=1);

namespace App\Common;

use Vectorface\GoogleAuthenticator;

class GoogleCode
{

    /**
     * @var string
     */
    const PLATFORM = 'CD支付系统后台';
    /**
     * @var string
     */
    const MERCHANT = 'CD支付商户后台';

    /**
     * @var GoogleAuthenticator
     */
    private static ?GoogleAuthenticator $auth = null;

    /**
     * Instance
     */
    private static function getInstance(): GoogleAuthenticator
    {
        if (!self::$auth) {
            self::$auth = new GoogleAuthenticator();
        }
        return self::$auth;
    }

    /**
     * 创建密钥
     */
    public static function secret(): string
    {
        return self::getInstance()->createSecret();
    }

    /**
     *  依据名称生成url
     */
    public static function  getQRCodeURL(string $secret, string $name): string
    {
        return self::getInstance()->getQRCodeUrl($name, $secret);
    }

    /**
     * 生成平台google验证码
     */
    public static function  getPlatformQRCodeURL(string $secret): string
    {
        return self::getInstance()->getQRCodeUrl(self::PLATFORM, $secret);
    }

    /**
     * 生成商户后台google验证码
     */
    public static function  getMerchantQRCodeURL(string $secret): string
    {
        return self::getInstance()->getQRCodeUrl(self::MERCHANT, $secret);
    }

    /**
     * 生成code
     */
    public static function getCode(string $secret): string
    {
        return self::getInstance()->getCode($secret);
    }

    /**
     * 校验google验证码
     */
    public static function verifyCode(string $secret, string $code): bool
    {
        return self::getInstance()->verifyCode($secret, $code, 2);
    }
}
