<?php
declare(strict_types=1);

namespace App\Common;

use Hyperf\Utils\ApplicationContext;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class Validation
{
    /**
     * @var ?ValidatorFactoryInterface
     */
    private static ?ValidatorFactoryInterface $validator = null;

    /**
     * 生成一个校验器
     * @return ValidatorFactoryInterface
     */
    public static function getValidator(): ValidatorFactoryInterface
    {
        if (!self::$validator) {
            $container = ApplicationContext::getContainer();
            self::$validator = $container->get(ValidatorFactoryInterface::class);
        }

        return self::$validator;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param bool $firstError
     * @return Result
     */
    public static function make(array $data, array $rules, array $messages = [], bool $firstError = true): Result
    {
        if (empty($messages)) {
            $messages = self::messages();
        }
        if (self::$validator) {
            $valid = self::$validator->make($data, $rules, $messages);
            if ($valid->fails()) {
                $errors = $valid->errors();
                $error = $firstError ? $errors->first() : $errors;
                return Result::error($error);
            }
            return Result::ok();
        }
        return Result::error('校验错误');
    }

    /**
     * 处定义错误信息
     * @return array
     */
    public static function messages(): array
    {
        return [];
    }
}
