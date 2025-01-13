<?php

namespace Domain\Invoice\Exceptions;

use Domain\DomainException;
use JetBrains\PhpStorm\Pure;

class CannotBeMarkedAsDeliveredException extends DomainException
{
    #[Pure] public function __construct(string $status)
    {
        parent::__construct('An invoice can only be marked as delivered if its current status is ' . $status, 0, null);
    }

}

