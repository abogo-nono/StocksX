<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Mail\LowStockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendLowStockEmailAlertController extends Controller
{
    public function store(Request $request)
    {
        $lowStockProducts = Product::where('quantity', '<=', 10)->get(['name', 'quantity']);

        if ($lowStockProducts->isNotEmpty()) {
            $adminUser = User::find(1, ['name', 'email']);

            $emailData = [
                // 'subject' => 'Low Stocks Alert',
                'products' => $lowStockProducts,
                'user' => $adminUser,
            ];

            Mail::send(new LowStockAlert($emailData));
        }

        return to_route('filament.admin.resources.orders.index');
    }
}
