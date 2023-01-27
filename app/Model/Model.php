<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

abstract class Model extends BaseModel implements CacheableInterface
{
    use Cacheable;

    /**
     * @var string[]
     */
    public static array $autoFields = ['created', 'updated'];
    /**
     * 修改时间相关为数字
     * @var ?string
     */
    protected ?string $dateFormat = 'U';
    /**
     * @var ?string
     */
    protected ?string $connection = 'default';
    protected array $fillable = ['created', 'updated'];
    //protected $casts = ['id' => 'integer'];

    /**
     * 创建记录之前处理
     * @param array $data
     * @return bool
     */
    public static function beforeCreate(array &$data): bool
    {
        if (in_array('created', static::$autoFields)) {
            $data['created'] = time();
        }
        if (in_array('updated', static::$autoFields)) {
            $data['updated'] = time();
        }
        return true;
    }

    /**
     * 创建记录之后处理
     * @param array $data
     */
    public static function afterCreate(array $data)
    {
    }

    /**
     * 修改记录之前处理
     * @param array $data
     * @return bool
     */
    public static function beforeUpdate(array &$data): bool
    {
        if (in_array('updated', self::$autoFields)) {
            $data['updated'] = time();
        }
        return true;
    }

    /**
     * 修改记录之后处理
     * @param array $data
     */
    public static function afterUpdate(array $data)
    {
    }

    /**
     * 保存记录之前处理
     * @param array $data
     * @return bool
     */
    public static function beforeSave(array &$data): bool
    {
        return true;
    }

    /**
     * 保存记录之后处理
     * @param array $data
     */
    public static function afterSave(array $data)
    {
    }

    public static function findByID($id)
    {
        return self::where(['id' => $id])->first();
    }
}
