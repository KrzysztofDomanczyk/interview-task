<?php

namespace Domain\Invoice\DTOs;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;

class CreateInvoiceDTO
{
    public function __construct(
        public StatusEnum $status,
        public string $customer_name,
        public string $customer_email,
        /**
         * @var Collection<CreateInvoiceProductLineDTO>
         */
        public Collection $product_lines
    ) {}
}
