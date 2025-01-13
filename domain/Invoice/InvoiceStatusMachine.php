<?php

namespace Domain\Invoice;

use Domain\Invoice\Aggregators\InvoiceAggregator;
use Modules\Invoices\Domain\Enums\StatusEnum;

class InvoiceStatusMachine
{
    protected StatusEnum $currentState;

    protected array $transitions = [
        StatusEnum::Draft->value => [StatusEnum::Sending],
        StatusEnum::Sending->value => [StatusEnum::SentToClient],
    ];

    public function __construct(StatusEnum $initialState = InvoiceAggregator::DEFAULT_STATUS)
    {
        $this->currentState = $initialState;
    }

    public function getCurrentState(): StatusEnum
    {
        return $this->currentState;
    }

    public function transitionTo(StatusEnum $newState): bool
    {
        if (in_array($newState, $this->transitions[$this->currentState->value] ?? [])) {
            $this->currentState = $newState;
            return true;
        }
        return false;
    }

    public function canBeTransitionTo(StatusEnum $newState): bool {
        return in_array($newState, $this->transitions[$this->currentState->value] ?? []);
    }
}
