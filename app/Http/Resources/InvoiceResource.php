<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $invoice = parent::toArray($request);

        $invoice['product_lines'] = $this->productLines->toArray();

        $invoice['total_price'] = $this->total_price;

        return $invoice;
    }
}
