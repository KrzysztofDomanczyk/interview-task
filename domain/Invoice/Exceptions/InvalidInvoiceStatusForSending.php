<?php

namespace Domain\Invoice\Exceptions;

use Domain\DomainException;
use JetBrains\PhpStorm\Pure;

class InvalidInvoiceStatusForSending extends DomainException
{
    #[Pure] public function __construct(string $currentStatus)
    {
        parent::__construct('Invoice cannot be marked as delivery in current status ' . $currentStatus, 0, null);
    }

}
