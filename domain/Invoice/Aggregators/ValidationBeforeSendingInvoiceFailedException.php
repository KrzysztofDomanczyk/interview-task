<?php

namespace Domain\Invoice\Aggregators;

use Domain\DomainException;
use JetBrains\PhpStorm\Pure;

class ValidationBeforeSendingInvoiceFailedException extends DomainException
{
    #[Pure] public function __construct()
    {
        parent::__construct('Cannot be sent, an invoice must contain product lines with both quantity and unit price as positive integers greater than zero.', 0, null);
    }

}
