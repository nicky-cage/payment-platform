<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property  int $id
 * @property  int $merchant_id
 * @property  int $payment_id
 * @property  int $rate
 * @property  int $created
 */
class MerchantPaymentLog extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = null;
    protected ?string $table = 'merchant_payment_logs';
    protected ?string $dateFormat = 'U';
}
