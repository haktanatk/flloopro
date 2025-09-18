<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class MyOrdersController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->latest()->paginate(20);

        return view('account.orders', compact('orders'));
    }
}
