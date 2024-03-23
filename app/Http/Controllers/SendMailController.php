<?php

namespace App\Http\Controllers;

use App\Models\User;
use Mail;
use App\Models\Product;
use App\Mail\SampleMail;
use Illuminate\Http\Request;

class SendMailController extends Controller
{

    public function index()
    {
        $products = Product::where('quantity', '<', 15)->get(['name', 'quantity']);
        $user = User::find(1)->get(['name', 'email']);

        // dd($user);

        $content = [
            'subject' => 'Low Stocks Alert',
            'body' => [$products, $user],
        ];


        try {
            Mail::to($user[0]->email)->send(new SampleMail($content));
        } catch (\Exception $e){

        }
        // return view('emails.low-stocks')->with(['products' => $products, 'user' => $user]);

        return to_route('filament.admin.auth.login');
    }
}
