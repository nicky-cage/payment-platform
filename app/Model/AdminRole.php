<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $remark
 * @property string $permission_list
 * @property int $created
 * @property int $updated
 * @property string $menu_ids
 */
class AdminRole extends Model
{
    protected ?string $table = 'admin_roles';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    /**
     * @return array
     */
    public static function getRelatedAll(): array
    {
        $result = [];
        $rows = self::all();
        foreach ($rows as $r) {
            $result[$r->id] = $r;
        }
        return $result;
    }
    /**
     * @param $id
     * @return array
     */
    public static function getOneId($id): array
    {
        $row = AdminRole::query()->find($id)->toArray();
        return $row;
    }
}
