<?php

declare(strict_types=1);

namespace App\Http\Common;

trait BaseJsonTrait
{

    /**
     * return json success
     * @return array
     */
    protected static function jsonOk(string $message = ""): array
    {
        return [
            'code' => 0,
            'message' => $message,
        ];
    }

    /**
     * @param string $message
     * @param int $code
     * @return array
     */
    protected static function jsonErr(string $message = '程序运行出现错误', int $code = 500): array
    {
        return [
            'code' => $code,
            'message' => $message,
        ];
    }

    /**
     * 默认返回带结果信息
     * @param array $data
     * @param string $message
     * @return array
     */
    protected static function jsonResult(array $data, string $message = ''): array
    {
        return self::jsonArray($data, $message);
    }

    /**
     * 默认返回带结果信息
     * @param array $data
     * @param string $message
     * @return array
     */
    protected static function jsonArray(array $data, string $message = ''): array
    {
        return [
            'code' => 0,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * 默认返回带结果信息
     * @param object $data
     * @param string $message
     * @return array
     */
    protected static function jsonObject(object $data, string $message = ''): array
    {
        return [
            'code' => 0,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * 默认返回带结果信息
     * @param array $data
     * @param string $message
     * @return array
     */
    protected static function jsonArrayResult(array $data, string $message = ''): array
    {
        return self::jsonArrays($data, $message);
    }

    /**
     * 默认返回带结果信息
     * @param array $data
     * @param string $message
     * @return array
     */
    protected static function jsonArrays(array $data, string $message = ''): array
    {
        return [
            'code' => 0,
            'message' => $message,
            'data' => json_encode($data),
        ];
    }
}
