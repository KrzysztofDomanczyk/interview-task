<?php

namespace Invoice\Aggregators;

use Domain\Invoice\Aggregators\InvoiceAggregator;
use Domain\Invoice\Aggregators\ValidationBeforeSendingInvoiceFailedException;
use Domain\Invoice\DTOs\CreateInvoiceDTO;
use Domain\Invoice\DTOs\CreateInvoiceProductLineDTO;
use Domain\Invoice\Exceptions\InvalidInvoiceInitialStatusException;
use Domain\Invoice\Exceptions\InvalidInvoiceStatusForSending;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class InvoiceAggregatorTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected Dispatcher $dispatcher;

    public function testInvoiceCanBeCreatedOnlyWithDraftStatus(): void
    {
        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $this->faker->email(),
            collect()
        );

        $aggregator = InvoiceAggregator::create($newInvoiceDTO);

        $this->assertSame(StatusEnum::Draft, $aggregator->returnInvoice()->status);

        $this->expectException(InvalidInvoiceInitialStatusException::class);

        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Sending,
            $this->faker->name(),
            $this->faker->email(),
            collect()
        );

        InvoiceAggregator::create($newInvoiceDTO);
    }

    public function testInvoiceCanBeCreatedWithEmptyProductLines(): void
    {
        $email = $this->faker->email();

        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $email,
            collect()
        );

        InvoiceAggregator::create($newInvoiceDTO);

        $this->assertDatabaseHas('invoices', [
            'customer_email' => $email,
        ]);
    }

    public function testAnInvoiceCanBeSentOnlyInDraftStatus(): void
    {
        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $this->faker->email(),
            collect( [
                new CreateInvoiceProductLineDTO(
                    'Test1',
                    1,
                    10
                )
            ])
        );

        $aggregator = InvoiceAggregator::create($newInvoiceDTO);

        $aggregator->sent();

        $this->assertSame(StatusEnum::Sending, $aggregator->returnInvoice()->status);

        $this->expectException(InvalidInvoiceStatusForSending::class);

        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $this->faker->email(),
            collect( [
                new CreateInvoiceProductLineDTO(
                    'Test1',
                    1,
                    10
                )
            ])
        );

        $aggregator = InvoiceAggregator::create($newInvoiceDTO);

        $aggregator->setStatus(StatusEnum::Sending);

        $aggregator->sent();

        $this->assertSame(StatusEnum::Sending, $aggregator->returnInvoice()->status);
    }

    public function testAnInvoiceCanBeMarkedAsDeliveryWhenSendingStatusIsSet(): void
    {
        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $this->faker->email(),
            collect( [
                new CreateInvoiceProductLineDTO(
                    'Test1',
                    1,
                    10
                )
            ])
        );

        $aggregator = InvoiceAggregator::create($newInvoiceDTO);

        $aggregator->sent();

        Event::dispatch(new ResourceDeliveredEvent(Uuid::fromString($aggregator->returnInvoice()->id)));

        $aggregator = InvoiceAggregator::get(Uuid::fromString($aggregator->returnInvoice()->id));

        $this->assertSame(StatusEnum::SentToClient, $aggregator->returnInvoice()->status);

        $this->expectException(InvalidInvoiceStatusForSending::class);

        Event::dispatch(new ResourceDeliveredEvent(Uuid::fromString($aggregator->returnInvoice()->id)));
    }

    public function testAnInvoiceMustContainProductLinesWithBothQuantityAndUnitPriceAsPositiveIntegersGreaterThanZero(): void
    {
        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $this->faker->email(),
            collect(
                [
                    new CreateInvoiceProductLineDTO(
                        'Test1',
                        1,
                        10
                    )
                ]
            )
        );

        $aggregator = InvoiceAggregator::create($newInvoiceDTO);

        $aggregator->sent();

        $this->assertSame($aggregator->returnInvoice()->status, StatusEnum::Sending);

        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $this->faker->email(),
            collect(
                [
                    new CreateInvoiceProductLineDTO(
                        'Test1',
                        0,
                        10
                    )
                ]
            )
        );

        $aggregator = InvoiceAggregator::create($newInvoiceDTO);

        $this->expectException(ValidationBeforeSendingInvoiceFailedException::class);

        $aggregator->sent();

        $this->expectException(ValidationBeforeSendingInvoiceFailedException::class);

        $newInvoiceDTO = new CreateInvoiceDTO(
            StatusEnum::Draft,
            $this->faker->name(),
            $this->faker->email(),
            collect(

            )
        );

        $aggregator = InvoiceAggregator::create($newInvoiceDTO);

        $aggregator->sent();

    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();

        $this->dispatcher = resolve(Dispatcher::class);
    }

    //Would be good to add another tests for status machine

}
