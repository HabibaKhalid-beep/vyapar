<?php

namespace App\Support;

use App\Models\AppSetting;

class TransactionNumberPrefix
{
    public static function defaults(): array
    {
        return [
            'invoice' => '',
            'estimate' => 'EST-',
            'proforma_invoice' => 'PI-',
            'payment_in' => 'PAY-',
            'delivery_challan' => 'DC-',
            'sale_order' => 'SO-',
            'purchase_order' => 'PO-',
            'credit_note' => 'CN-',
            'purchase_bill' => 'PB-',
            'purchase_return' => 'PR-',
        ];
    }

    public static function labels(): array
    {
        return [
            'invoice' => 'Invoice',
            'estimate' => 'Estimate',
            'proforma_invoice' => 'Proforma Invoice',
            'payment_in' => 'Payment-In',
            'delivery_challan' => 'Delivery Challan',
            'sale_order' => 'Sale Order',
            'purchase_order' => 'Purchase Order',
            'credit_note' => 'Credit Note',
            'purchase_bill' => 'Purchase Bill',
            'purchase_return' => 'Purchase Return',
        ];
    }

    public static function settingKey(string $type): string
    {
        return 'txn_prefix_' . $type;
    }

    public static function get(string $type): string
    {
        $defaults = static::defaults();
        return (string) AppSetting::getValue(static::settingKey($type), $defaults[$type] ?? '');
    }

    public static function all(): array
    {
        $prefixes = [];
        foreach (array_keys(static::defaults()) as $type) {
            $prefixes[$type] = static::get($type);
        }

        return $prefixes;
    }

    public static function format(string $type, int $nextNumber): string
    {
        return static::get($type) . $nextNumber;
    }
}
