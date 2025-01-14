<?php

namespace Domain\Invoice\Aggregators;

use Domain\Invoice\DTOs\CreateInvoiceDTO;
use Domain\Invoice\DTOs\CreateInvoiceProductLineDTO;
use Domain\Invoice\Exceptions\InvalidInvoiceInitialStatusException;
use Domain\Invoice\Exceptions\InvalidInvoiceStatusForSending;
use Domain\Invoice\InvoiceStatusMachine;
use Domain\Invoice\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Ramsey\Uuid\Uuid;

class InvoiceAggregator
{
    const DEFAULT_STATUS = StatusEnum::Draft;

    protected InvoiceStatusMachine $statusMachine;

    public function __construct(
        protected Invoice $invoice
    ) {
        $this->statusMachine = new InvoiceStatusMachine($this->invoice->status);
    }

    public static function get(string $uuid): InvoiceAggregator
    {
        return new self(Invoice::findOrFail($uuid));
    }

    public static function create(CreateInvoiceDTO $DTO): InvoiceAggregator
    {
        if (self::DEFAULT_STATUS->name != $DTO->status->name) {
            throw new InvalidInvoiceInitialStatusException(self::DEFAULT_STATUS->value);
        }

        $invoice = DB::transaction(function () use ($DTO) {

            $invoice = Invoice::create([
                'status' => $DTO->status,
                'customer_email' => $DTO->customer_email,
                'customer_name' => $DTO->customer_name,
            ]);

            $DTO->product_lines->map(function (CreateInvoiceProductLineDTO $lineDTO) use ($invoice) {
                $invoice->productLines()->create([
                    'invoice_id' => $invoice->id,
                    'product_name' => $lineDTO->product_name,
                    'quantity' => $lineDTO->quantity,
                    'unit_price' => $lineDTO->unit_price,
                    // In production app version, I would use Money PHP
                    'total_unit_price' => $lineDTO->unit_price * $lineDTO->quantity,
                ]);
            });

            return $invoice;

        });

        return new self($invoice);
    }

    public function returnInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function sent()
    {
        /**
         * @var NotificationFacade $notificationService
         */
        $notificationService = resolve(NotificationFacadeInterface::class);

        $this->validateBeforeSending();

        if ($this->statusMachine->canBeTransitionTo(StatusEnum::Sending)) {

            $notificationService->notify(new NotifyData(
                Uuid::fromString($this->invoice->id),
                'example@email.com',
                'Email has been sent',
                'Your invoice in attachments'
            ));

            $this->setStatus(StatusEnum::Sending);
        } else {
            throw new InvalidInvoiceStatusForSending($this->statusMachine->getCurrentState()->value);
        }

    }

    private function validateBeforeSending()
    {
        if ($this->invoice->productLines()->count() == 0) {
            throw new ValidationBeforeSendingInvoiceFailedException;
        }

        foreach ($this->invoice->productLines()->cursor() as $productLine) {
            if ($productLine->quantity <= 0 || $productLine->unit_price <= 0) {
                throw new ValidationBeforeSendingInvoiceFailedException;
            }
        }
    }

    public function setStatus(StatusEnum $status)
    {
        $changed = $this->statusMachine->transitionTo($status);

        if ($changed) {
            $this->invoice->status = $status;
            $this->invoice->save();
        }

        return $changed;
    }

    public function markAsDelivered(): void
    {
        $changed = $this->setStatus(StatusEnum::SentToClient);

        if (! $changed) {
            throw new InvalidInvoiceStatusForSending($this->statusMachine->getCurrentState()->value);
        }

    }
}
