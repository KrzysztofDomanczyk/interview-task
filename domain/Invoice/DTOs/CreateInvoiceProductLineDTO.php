<?php

namespace Domain\Invoice\DTOs;

class CreateInvoiceProductLineDTO
{
    public function __construct(
        public string $product_name,
        public int $quantity,
        // For simplicity, I used float. In the production version of the application I would use, for example, Money PHP with int type (storing cents)
        public float $unit_price,
    ) {}
}
