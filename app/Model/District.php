<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $city_code
 */
class District extends Model
{
    protected ?string $table = 'districts';
    public bool $timestamps = false;
    /**
     * @return array
     */
    public static function getRelatedAll(): array
    {
        $result = [];
        $rows = self::all();
        foreach ($rows as $r) {
            $result[$r->id] = ['name' => $r->name, 'code' => $r->code];
        }
        return $result;
    }
}
