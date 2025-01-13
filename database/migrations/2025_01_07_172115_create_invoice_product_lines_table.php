<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_product_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->constrained('invoices')->onDelete('cascade'); // Relacja z fakturą
            $table->string('product_name');
            $table->integer('quantity')->unsigned();
            //For simplicity, I used float. In the production version of the application I would use, for example, Money PHP with int type (storing cents)
            $table->float('unit_price')->unsigned();
            $table->float('total_unit_price')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_product_lines');
    }
};
