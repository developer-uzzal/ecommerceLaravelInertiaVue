<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Invoices;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use App\Models\CustomerProfile;
use App\Models\InvoiceProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InvoicesController extends Controller
{
    function index()
    {
        $invoices = Invoice::with(['customer', 'shippingMethod', 'items.product'])->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'message' => $invoices
        ]);
    }

    function makeOrder(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string',
                'address' => 'required|string',
                'note' => 'nullable|string',
                'paymentMethod' => 'required|string',
                'subTotal' => 'required|numeric',
                'shippingId' => 'required|exists:shipping_methods,id',
                'items' => 'required|array',
            ]);

            $name = $request->name;
            $phone = $request->phone;
            $address = $request->address;
            $note = $request->note;
            $paymentMethod = $request->paymentMethod;
            $subTotal = $request->subTotal;
            $shippingId = $request->shippingId;
            $items = $request->items;

            $shippingPrice = ShippingMethod::find($shippingId)->price;
            $total = $shippingPrice + $subTotal;

            $user = User::where('id', $request->userId)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'username' => $phone . time(),
                    'role' => 'user',
                    'email' => $phone . time() . '@gmail.com',
                    'password' => Hash::make($phone),
                ]);
            }

            $customer = CustomerProfile::where('phone', $phone)->first();
            if (!$customer) {
                $customer = CustomerProfile::create([
                    'user_id' => $user->id,
                    'phone' => $phone,
                ]);
            }

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'invoice_no' => str()->random(10) . time(),
                'name' => $name,
                'ship_phone' => $phone,
                'ship_add' => $address,
                'note' => $note,
                'payment_method' => $paymentMethod,
                'shipping_method_id' => $shippingId,
                'total' => $total,
                'payable' => $total,
                'ship_country' => 'bangladesh',
                'ship_state' => 'dhaka',

            ]);

            foreach ($items as $item) {
                InvoiceProduct::create([
                    'invoice_id' => $invoice->id,
                    'color' => $item['colorLevel'],
                    'size' => $item['sizeLevel'],
                    'product_id' => $item['product']['id'],
                    'qty' => $item['quantity'],
                    'sale_price' => $item['product']['sale_price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice created successfully',
                'invoiceId' => $invoice->invoice_no
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    function showInvoice($id)
    {
        $invoice = Invoice::with([
            'items.product:id,title',
            'customer.user:id,name',
            'shippingMethod',

        ])->where('invoice_no', $id)->first();

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice found successfully',
            'invoice' => $invoice
        ], 200);
    }

    public function showInvoiceSummary($id)
    {
        $user = User::with(['profile.invoices.items'])
            ->where('id', $id)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        if (!$user->profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'User profile not found'
            ], 404);
        }

        $invoices = $user->profile->invoices->map(function ($invoice) {
            return [
                'id' => $invoice->invoice_no,
                'total' => $invoice->total,
                'item_count' => $invoice->items->count(),
                'created_at' => $invoice->created_at,
                'order_status' => $invoice->order_status,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'customer_profile' => $user->profile,
                'invoices' => $invoices,
            ]
        ]);
    }


    function updateStatus(Request $request, $id)
    {
   
        try {
            $invoice = Invoice::where('invoice_no', $id)->first();

            if (!$invoice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invoice not found'
                ], 404);
            }

            $invoice->order_status = $request->status;

            $invoice->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice status updated successfully'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }
}
