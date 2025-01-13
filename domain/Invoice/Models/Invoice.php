<?php

namespace Domain\Invoice\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Invoices\Domain\Enums\StatusEnum;

class Invoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'status',
        'customer_name',
        'customer_email',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusEnum::class,
        ];
    }

    public function productLines()
    {
        return $this->hasMany(InvoiceProductLine::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->productLines->sum(function ($line) {
            return $line->quantity * $line->unit_price;
        });
    }
}
