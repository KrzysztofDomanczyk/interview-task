<?php

namespace Domain\Invoice\Exceptions;

use Domain\DomainException;
use JetBrains\PhpStorm\Pure;

class InvalidInvoiceInitialStatusException extends DomainException
{
    #[Pure] public function __construct(string $defaultStatus)
    {
        parent::__construct('Inovice can be created only with status ' . $defaultStatus, 0, null);
    }

}
