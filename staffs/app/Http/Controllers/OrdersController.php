<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = Orders::query()->with('user', 'address', 'product'); // Eager load relationships
    
        // Check if there's a search query
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('address', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('price', 'like', "%{$search}%")
              ->orWhere('delivery_charges', 'like', "%{$search}%")
              ->orWhere('payment_mode', 'like', "%{$search}%");
        }
    
        // Paginate the results
        $orders = $query->latest()->paginate(10);
    
        $users = Users::all(); // Fetch all users for the filter dropdown
        $products = Products::all(); // Fetch all products for the filter dropdown
    
        return view('orders.index', compact('users', 'orders', 'products')); // Pass users, orders, and products to the view
    }
    

    public function destroy(Orders $orders)
    {
        $orders->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
