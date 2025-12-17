<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates customers table exactly as specified in DBML
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('email', 50)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('account_holder', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('bank_name', 50)->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Unique indexes as per DBML
            $table->unique(['tenant_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
