<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $bank_id
 * @property string $bank_name
 * @property string $bank_code
 * @property string $branch_name
 * @property string $card_number
 * @property string $real_name
 * @property float $each_min
 * @property float $each_max
 * @property float $pay_max
 * @property int $call_count
 * @property int $created
 * @property int $updated
 */
class Card extends Model
{
    protected ?string $table = 'cards';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    /**
     * @param array $data
     * @return bool
     */
    public static function beforeSave(array &$data): bool
    {
        $bankId = $data['bank_id'];
        $bank = Bank::query()->find($bankId);
        $data['bank_name'] = $bank->name;
        $data['bank_code'] = $bank->code;
        return parent::beforeSave($data);
    }
}
