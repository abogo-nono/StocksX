<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates invoices table exactly as specified in DBML
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 255);
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('tenant_id');

            // Invoice Details
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('draft');

            // Financial Data
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            // Payment Tracking
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);

            // Customization
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('invoice_template', 50)->default('default');

            // Audit
            $table->unsignedBigInteger('created_by');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // DBML indexes
            $table->unique(['tenant_id', 'invoice_no']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
