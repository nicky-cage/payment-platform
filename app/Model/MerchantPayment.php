<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property  int $id
 * @property  int $merchant_id
 * @property  int $payment_id
 * @property  int $rate
 * @property  int $created
 * @property  int $updated
 */
class MerchantPayment extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    protected ?string $table = 'merchant_payments';
    protected ?string $dateFormat = 'U';
}
