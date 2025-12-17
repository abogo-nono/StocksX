<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Updates products table to match DBML specification exactly
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop existing image and price columns not in DBML
            if (Schema::hasColumn('products', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }

            // Rename foreign key columns to match DBML
            if (Schema::hasColumn('products', 'product_categories_id')) {
                $table->renameColumn('product_categories_id', 'category_id');
            }
            if (Schema::hasColumn('products', 'product_suppliers_id')) {
                $table->renameColumn('product_suppliers_id', 'supplier_id');
            }

            // Add missing columns from DBML
            if (!Schema::hasColumn('products', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->after('unit_id');
            }

            // Add foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('product_suppliers')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');

            // Add DBML indexes
            $table->unique(['tenant_id', 'slug']);
            $table->unique(['tenant_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop indexes
            $table->dropUnique(['tenant_id', 'slug']);
            $table->dropUnique(['tenant_id', 'code']);

            // Drop foreign keys
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['unit_id']);

            // Remove tenant_id
            $table->dropColumn('tenant_id');

            // Rename back foreign keys
            $table->renameColumn('category_id', 'product_categories_id');
            $table->renameColumn('supplier_id', 'product_suppliers_id');

            // Add back old columns
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
        });
    }
};
