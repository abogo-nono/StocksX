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
        Schema::table('order_details', function (Blueprint $table) {
            // Add extended fields to support rich order detail functionality
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('product_code')->nullable()->after('product_name');
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity'); // Alias for unitcost
            $table->decimal('total_price', 10, 2)->nullable()->after('total'); // Alias for total
            $table->decimal('tax_rate', 5, 2)->default(0)->after('total_price');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            $table->text('notes')->nullable()->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn([
                'product_name',
                'product_code',
                'unit_price',
                'total_price',
                'tax_rate',
                'tax_amount',
                'notes'
            ]);
        });
    }
};
