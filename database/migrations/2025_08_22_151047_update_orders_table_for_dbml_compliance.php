<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Updates orders table to match DBML specification exactly
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add missing columns from DBML
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            if (!Schema::hasColumn('orders', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'client_name')) {
                $table->string('client_name', 255)->after('customer_id');
            }
            if (!Schema::hasColumn('orders', 'client_phone')) {
                $table->string('client_phone', 15)->nullable()->after('client_name');
            }
            if (!Schema::hasColumn('orders', 'client_address')) {
                $table->string('client_address', 255)->nullable()->after('client_phone');
            }
            if (!Schema::hasColumn('orders', 'order_status')) {
                $table->tinyInteger('order_status')->default(1)->after('order_date');
            }
            if (!Schema::hasColumn('orders', 'delivered')) {
                $table->boolean('delivered')->default(false)->after('order_status');
            }
            if (!Schema::hasColumn('orders', 'total_products')) {
                $table->integer('total_products')->after('delivered');
            }
            if (!Schema::hasColumn('orders', 'sub_total')) {
                $table->decimal('sub_total', 10, 2)->after('total_products');
            }
            if (!Schema::hasColumn('orders', 'vat')) {
                $table->decimal('vat', 10, 2)->default(0)->after('sub_total');
            }
            if (!Schema::hasColumn('orders', 'order_no')) {
                $table->string('order_no', 255)->after('vat');
            }
            if (!Schema::hasColumn('orders', 'payment_type')) {
                $table->string('payment_type', 50)->default('cash')->after('total');
            }
            if (!Schema::hasColumn('orders', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->after('payment_type');
            }

            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Add DBML indexes
            $table->unique(['tenant_id', 'order_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
