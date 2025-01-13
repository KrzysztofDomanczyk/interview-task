<?php

namespace Domain\Invoice\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProductLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'invoice_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_unit_price',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
