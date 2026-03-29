<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('client_id')
                ->nullable()
                ->after('user_id')
                ->constrained('clients')
                ->nullOnDelete();
        });

        $clientIdsBySignature = [];

        DB::table('orders')
            ->select(['id', 'client_name', 'client_phone', 'client_address'])
            ->orderBy('id')
            ->chunkById(100, function ($orders) use (&$clientIdsBySignature): void {
                foreach ($orders as $order) {
                    if (blank($order->client_name) && blank($order->client_phone) && blank($order->client_address)) {
                        continue;
                    }

                    $signature = md5(json_encode([
                        $order->client_name,
                        $order->client_phone,
                        $order->client_address,
                    ]));

                    if (! isset($clientIdsBySignature[$signature])) {
                        $clientIdsBySignature[$signature] = DB::table('clients')
                            ->where('name', $order->client_name)
                            ->where(function ($query) use ($order): void {
                                $order->client_phone === null
                                    ? $query->whereNull('phone')
                                    : $query->where('phone', $order->client_phone);
                            })
                            ->where(function ($query) use ($order): void {
                                $order->client_address === null
                                    ? $query->whereNull('address')
                                    : $query->where('address', $order->client_address);
                            })
                            ->value('id');
                    }

                    if (! $clientIdsBySignature[$signature]) {
                        $clientIdsBySignature[$signature] = DB::table('clients')->insertGetId([
                            'name' => $order->client_name ?: 'Walk-in customer',
                            'phone' => $order->client_phone,
                            'address' => $order->client_address,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['client_id' => $clientIdsBySignature[$signature]]);
                }
            });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'client_name',
                'client_phone',
                'client_address',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('user_id');
            $table->string('client_phone')->nullable()->after('client_name');
            $table->string('client_address')->nullable()->after('client_phone');
        });

        DB::table('orders')
            ->leftJoin('clients', 'clients.id', '=', 'orders.client_id')
            ->select([
                'orders.id as order_id',
                'clients.name',
                'clients.phone',
                'clients.address',
            ])
            ->orderBy('orders.id')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    DB::table('orders')
                        ->where('id', $order->order_id)
                        ->update([
                            'client_name' => $order->name,
                            'client_phone' => $order->phone,
                            'client_address' => $order->address,
                        ]);
                }
            }, 'orders.id', 'order_id');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });

        Schema::dropIfExists('clients');
    }
};
