<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            // Product details
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();

            // Prices
            $table->decimal('cost_price', 12, 2);
            $table->decimal('selling_price', 12, 2);

            // Product discount
            $table->string('discount_type', 20)->default('percentage');
            $table->decimal('discount_value', 12, 2)->default(0);

            // Stock
            $table->unsignedInteger('quantity')->default(0);

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
