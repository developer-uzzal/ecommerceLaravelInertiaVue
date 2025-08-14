<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Cache;

class ShippingMethodsController extends Controller
{
    public function shippingMethodsAll()
    {
        $shippingMethods = Cache::remember('active_shipping_methods', 60 * 24, function () {
            return ShippingMethod::where('status', 1)->get();
        });

        if ($shippingMethods->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shipping methods not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => $shippingMethods,
        ], 200);
    }
    function index()
    {

        $shippingMethods = ShippingMethod::all();
        if ($shippingMethods) {
            return response()->json([
                'status' => 'success',
                'message' => $shippingMethods,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Shipping methods not found',
            ], 200);
        }
    }

    function store(Request $request)
    {

        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
            ]);

            $shippingMethod = ShippingMethod::create([
                'name' => $request->name,
                'price' => $request->price,
                'status' => $request->status ? 1 : 0

            ]);

            if ($shippingMethod) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Shipping method created successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shipping method not created',
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    function show($id)
    {

        $shippingMethod = ShippingMethod::where('id', $id)->first();
        if ($shippingMethod) {
            return response()->json([
                'status' => 'success',
                'message' => $shippingMethod,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Shipping method not found',
            ], 200);
        }
    }

    function update(Request $request, $id)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
            ]);

            $shippingMethod = ShippingMethod::find($id);
            $shippingMethod->name = $request->name;
            $shippingMethod->price = $request->price;
            $shippingMethod->status = $request->status ? 1 : 0;
            $shippingMethod->save();

            if ($shippingMethod) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Shipping method updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shipping method not updated',
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    function destroy($id)
    {

        $shippingMethod = ShippingMethod::find($id);
        if ($shippingMethod) {
            $shippingMethod->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Shipping method deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Shipping method not deleted',
            ], 200);
        }
    }
}
