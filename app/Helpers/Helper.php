<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Model\Menu;

class Helper
{
    /**
     * @param int $level
     * @return string
     */
    public static function menuLevel(int $level): string
    {
        return Menu::$menuLevels[$level] ?? '';
    }
}
