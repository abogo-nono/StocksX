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
        // Check if tenant_id column exists
        if (!\Schema::hasColumn('product_categories', 'tenant_id')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            });

            // Update existing records to have the first tenant's ID
            $firstTenant = \App\Models\Tenant::first();
            if ($firstTenant) {
                \DB::table('product_categories')->whereNull('tenant_id')->update(['tenant_id' => $firstTenant->id]);
            }

            Schema::table('product_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
            });
        }

        // Add foreign key and index if they don't exist
        Schema::table('product_categories', function (Blueprint $table) {
            try {
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
            try {
                $table->index(['tenant_id', 'slug']);
            } catch (\Exception $e) {
                // Index might already exist
            }
        });
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id', 'slug']);
            $table->dropColumn('tenant_id');
        });
    }
};
