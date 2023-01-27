<?php
declare(strict_types=1);

namespace App\Common;

class Result
{
    /**
     * @var int|null
     */
    public ?int $value = 1;

    /**
     * @var string
     */
    public string $message = '';

    /**
     * @param mixed $value
     * @return Result
     */
    public static function ok($value = null): Result
    {
        $result = new Result();
        if (!$value) {
            $result->value = $value;
        }
        return $result;
    }

    /**
     * @param string $message
     * @return Result
     */
    public static function error(string $message = ''): Result
    {
        $result = new Result();
        $result->value = null;
        if ($message) {
            $result->message = $message;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->value == null;
    }

    /**
     * @return bool
     */
    public function isErr(): bool
    {
        return $this->value != null;
    }
}
