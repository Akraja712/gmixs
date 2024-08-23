<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users; 
use App\Models\Products; 
use App\Models\Address; 
use App\Models\Orders;
use App\Models\Friends; 
use App\Models\Points; 
use App\Models\Plans;
use App\Models\Notifications; 
use App\Models\Verifications; 
use App\Models\Transaction; 
use App\Models\Feedback;
use App\Models\Fakechats; 
use App\Models\Professions; 
use App\Models\RechargeTrans;
use App\Models\VerificationTrans;
use App\Models\Appsettings; 
use App\Models\News; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Berkayk\OneSignal\OneSignalClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class AuthController extends Controller
{
 
    public function login(Request $request)
    {
        // Retrieve phone number from the request
        $mobile = $request->input('mobile');

        if (empty($mobile)) {
            $response['success'] = false;
            $response['message'] = 'mobile is empty.';
            return response()->json($response, 400);
        }

        // Remove non-numeric characters from the phone number
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        // Check if the length of the phone number is not equal to 10
        if (strlen($mobile) !== 10) {
            $response['success'] = false;
            $response['message'] = "mobile number should be exactly 10 digits";
            return response()->json($response, 400);
        }


        // Check if a customer with the given phone number exists in the database
        $user = Users::where('mobile', $mobile)->first();

        // If customer not found, register the user
        if (!$user) {
            $user = new Users();
            $user->mobile = $mobile;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'registered' => true,
            'message' => 'Logged in successfully.',
        ], 200);
    }
    public function product_list(Request $request)
    {
        $products = Products::orderBy('price', 'desc')->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found.',
            ], 404);
        }

        $productsDetails = [];

        foreach ($products as $product) {
            $productsDetails[] = [
                'id' => $product->id,
                'name' => $product->name,
                'unit' => $product->unit,
                'measurement' => $product->measurement,
                'price' => (string) $product->price,
                'updated_at' => Carbon::parse($product->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($product->created_at)->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'products Details retrieved successfully.',
            'data' => $productsDetails,
        ], 200);
    }
    public function add_address(Request $request)
    {
        $user_id = $request->input('user_id'); 
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $alternate_mobile = $request->input('alternate_mobile');
        $door_no = $request->input('door_no');
        $street_name = $request->input('street_name');
        $city = $request->input('city');
        $pincode = $request->input('pincode');
        $state = $request->input('state');

        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 400);
        }

        if (empty($name)) {
            return response()->json([
                'success' => false,
                'message' => 'name is empty.',
            ], 400);
        }

        if (empty($alternate_mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Alternate Mobile is empty.',
            ], 400);
        }

        if (empty($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'mobile is empty.',
            ], 400);
        }

        if (empty($door_no)) {
            return response()->json([
                'success' => false,
                'message' => 'door_no is empty.',
            ], 400);
        }

        if (empty($street_name)) {
            return response()->json([
                'success' => false,
                'message' => 'street_name is empty.',
            ], 400);
        }

        if (empty($city)) {
            return response()->json([
                'success' => false,
                'message' => 'city is empty.',
            ], 400);
        }

        if (empty($pincode)) {
            return response()->json([
                'success' => false,
                'message' => 'pincode is empty.',
            ], 400);
        }

        if (empty($state)) {
            return response()->json([
                'success' => false,
                'message' => 'state is empty.',
            ], 400);
        }

        // Check if user exists
        $user = Users::find($user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'user not found.',
            ], 404);
        }

        // Create a new Address instance
        $address = new Address();
        $address->user_id = $user_id; 
        $address->name = $name;
        $address->mobile = $mobile;
        $address->alternate_mobile = $alternate_mobile;
        $address->door_no = $door_no;
        $address->street_name = $street_name;
        $address->city = $city;
        $address->pincode = $pincode;
        $address->state = $state;

        // Save the address
        if (!$address->save()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save address.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
        ], 201);
    }

    public function place_order(Request $request)
    {
        $user_id = $request->input('user_id'); 
        $product_id = $request->input('product_id');
        $address_id = $request->input('address_id');
        $price = $request->input('price');
        $delivery_charges = $request->input('delivery_charges');
        $payment_mode = $request->input('payment_mode');

        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 400);
        }

        if (empty($product_id)) {
            return response()->json([
                'success' => false,
                'message' => 'product_id is empty.',
            ], 400);
        }

        if (empty($address_id)) {
            return response()->json([
                'success' => false,
                'message' => 'address_id is empty.',
            ], 400);
        }

        if (empty($price)) {
            return response()->json([
                'success' => false,
                'message' => 'price is empty.',
            ], 400);
        }

        if (empty($delivery_charges)) {
            return response()->json([
                'success' => false,
                'message' => 'delivery_charges is empty.',
            ], 400);
        }

        if (empty($payment_mode)) {
            return response()->json([
                'success' => false,
                'message' => 'payment_mode is empty.',
            ], 400);
        }

        // Check if user exists
        $user = Users::find($user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'user not found.',
            ], 404);
        }

        // Check if product exists
        $product = Products::find($product_id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'product not found.',
            ], 404);
        }

        // Check if address exists
        $address = Address::find($address_id);
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'address not found.',
            ], 404);
        }

        // Check if the order already exists
        $existingOrder = Orders::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('address_id', $address_id)
            ->first();

        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Order already exists.',
            ], 400);
        }

        // Check if payment mode is valid
        if ($payment_mode !== 'prepaid' && $payment_mode !== 'cod') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment mode. Payment mode should be either prepaid or cod.',
            ], 400);
        }

        // Insert into orders table
        $order = new Orders();
        $order->user_id = $user_id;
        $order->product_id = $product_id;
        $order->address_id = $address_id;
        $order->price = $price;
        $order->delivery_charges = $delivery_charges;
        $order->payment_mode = $payment_mode;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully.',
        ], 200);
    }

}