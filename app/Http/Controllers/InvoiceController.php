<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use Domain\Invoice\Aggregators\InvoiceAggregator;
use Domain\Invoice\Models\Invoice;
use Modules\Notifications\Api\NotificationFacadeInterface;


class InvoiceController
{
    public function __construct(NotificationFacadeInterface $notificationFacade)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        try {
            $invoice = InvoiceAggregator::create($request->toDTO())->returnInvoice();
        } catch (\DomainException $exception) {
            return response($exception->getMessage(), $exception->getCode());
        }

        return InvoiceResource::make($invoice);
    }


    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return InvoiceResource::make($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function sendInvoice(string $id)
    {
        try {

            $aggregator = InvoiceAggregator::get($id);

            $aggregator->sent();

        } catch (\DomainException $exception) {
            return response()->json([
                'success' => 'false',
                'message' => $exception->getMessage()
            ]);
        }

        return response()->json([
            'success' => 'true'
        ]);
    }
}
