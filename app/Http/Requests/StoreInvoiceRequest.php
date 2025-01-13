<?php

namespace App\Http\Requests;

use Domain\Invoice\DTOs\CreateInvoiceDTO;
use Domain\Invoice\DTOs\CreateInvoiceProductLineDTO;
use Domain\Invoice\Models\Invoice;
use Domain\Invoice\Models\InvoiceProductLine;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Invoices\Domain\Enums\StatusEnum;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:' . implode(',', array_column(StatusEnum::cases(), 'value')),
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'product_lines' => 'nullable|array',
            'product_lines.*.product_name' => 'required_with:product_lines|nullable|string|max:255',
            'product_lines.*.quantity' => 'required_with:product_lines|nullable|integer|min:1',
            'product_lines.*.unit_price' => 'required_with:product_lines|nullable|numeric|min:0.01',
        ];
    }

    public function toDTO(): CreateInvoiceDTO
    {
        $productLines =  collect($this->input('product_lines'))
            ->filter()->transform(function (array $item){
                  return new CreateInvoiceProductLineDTO(
                      $item['product_name'],
                      $item['quantity'],
                      $item['unit_price'],
                  );
            }) ?? collect();

        return new CreateInvoiceDTO(
            status: StatusEnum::tryFrom($this->status),
            customer_name: $this->customer_name,
            customer_email: $this->customer_email,
            product_lines: $productLines
        );
    }
}
