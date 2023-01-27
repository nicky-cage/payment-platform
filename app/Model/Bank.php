<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $remark
 * @property int $state
 * @property int $sort
 * @property string $icon
 * @property string $image
 */
class Bank extends Model
{
    protected ?string $table = 'banks';
    /**
     * @return array
     */
    public static function getAll(): array
    {
        $result = [];
        $rows = self::all();
        foreach ($rows as $r) {
            $result[$r->id] = $r->name;
        }
        return $result;
    }
}
