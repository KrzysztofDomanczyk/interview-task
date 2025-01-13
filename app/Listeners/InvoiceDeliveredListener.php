<?php

namespace App\Listeners;

use Domain\Invoice\Aggregators\InvoiceAggregator;
use Domain\Invoice\Models\Invoice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class InvoiceDeliveredListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ResourceDeliveredEvent $event): void
    {
        try {
            $aggregator = InvoiceAggregator::get($event->resourceId);
        } catch (ModelNotFoundException $exception) {
            return;
        }

        $aggregator->markAsDelivered();
    }
}
