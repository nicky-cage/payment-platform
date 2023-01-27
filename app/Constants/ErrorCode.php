<?php
declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;

class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;

    /**
     * @param int $code
     * @return string
     */
    public static function getMessage(int $code): string
    {
        return '';
    }
}
