<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;

use App\Models\Inventory;
use App\Models\Variation_option;
use App\Models\Product_delivery;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Order_item;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderReceiptNotification;

class OrdersController extends Controller
{    

    protected $dateTime;

    public function __construct()
    {
        $now = Carbon::now('Asia/Manila');
        $this->dateTime = $now->toDateTimeString();
    }

    public function track_orders(Request $request)
    {
        $status = $request->query('status');
        $search = request('search', null);
        $getVariationOptions = Variation_option::get();


        $query = Order_item::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id',  'products.id')
            ->join('categories', 'products.category_id', 'categories.id')
            ->join('product_deliveries', 'order_items.delivery_id', 'product_deliveries.id')
            ->join('users', 'orders.user_id', 'users.id')
            ->leftjoin('user_contacts', 'users.id', 'user_contacts.user_id')
            ->select(
                'orders.id as order_id',
                'orders.order_id as order_tracking_id',
                'orders.*',
                'order_items.id as order_item_id',
                'order_items.*',
                'products.id as prod_id',
                'products.*',
                'categories.name',
                'users.name as customer',
                'user_contacts.province',
                'user_contacts.city',
                'user_contacts.barangay',
                'user_contacts.landmark',
                'user_contacts.mobile_number',
                'product_deliveries.variation_option_id'
            )
            ->where('orders.status', $status)
            ->orderBy('orders.id', 'DESC');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.title', 'LIKE', "%$search%")
                    ->orWhere('orders.order_id', 'LIKE', "%$search%");
            });
        }

        $track_orders = $query->get();


        return response()->json(['track_orders' => $track_orders,'getVariationOptions' => $getVariationOptions], 200);

    }

    public function mark_order(Request $request)
    {
        $order_id = $request->input('order_id');
        $order_status = $request->input('order_status');

        if ($order_status == 0) {
            Order::where('id', $order_id)->update(['status' => 1]);
            $message = 'Order has been Placed';
            $tab = 1;
        } elseif ($order_status == 1) {
            Order::where('id', $order_id)->update(['status' => 2]);

            $orderItem = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')->where('orders.id', $order_id)->first();
            $inventoryItem = Inventory::where('delivery_id', $orderItem->delivery_id)->first();
            $inventoryItem->update([
                'qty_out' => $inventoryItem->qty_out + $orderItem['qty'],
                'updated_at' => $this->dateTime,
            ]);

            $countInv = $inventoryItem->qty_in - $inventoryItem->qty_out;
            if($countInv < 0){
              Product_delivery::where('id',$orderItem->delivery_id)->update([
               'status' => 0
              ]);
            }

            $message = 'Packaging the Order';
            $tab = 2;
        } elseif ($order_status == 2) {
            Order::where('id', $order_id)->update(['status' => 3]);
            $message = 'Order is ready to ship';
            $tab = 3;
        } elseif ($order_status == 3) {
            Order::where('id', $order_id)->update(['status' => 4]);
            $message = 'Order has been received by Customer';
            $tab = 4;
        } elseif ($order_status == 4) {
            Order::where('id', $order_id)->update(['status' => 5]);


            $receipt = Order::join('users', 'orders.user_id', '=', 'users.id')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('users.email', 'orders.order_id', 'orders.total_amount', 'products.title', 'products.price', 'products.shipping_fee', 'order_items.qty')
                ->where('orders.id', $order_id)->first();

            //Send email notification
            $receipt->notify(new OrderReceiptNotification($receipt));

            $message = 'Order successfully delivered';
            $tab = 5;
        }

        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Update',
            'activity_description' => $message,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);


        return response()->json(['message' => $message, 'tab' => $tab], 200);
    }


    public function cancel_order(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'notes' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');
        $notes = $request->input('notes');
        $now = Carbon::now('Asia/Manila');
        $dateTime = $now->toDateTimeString();
        // $find_cart = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')->where('orders.id', $id)->first();
       
      
        //     $find_inventory = Inventory::where('delivery_id', $find_cart->delivery_id)->first();
        //     $find_inventory->update(['qty_out' => $find_inventory->qty_out - $find_cart->qty]);
        //     if ($find_inventory) {
                $order = Order::find($id);
                $order->status = 7;
                $order->cancel_by = 2;
                $order->notes = $notes;
                $order->updated_at = $dateTime;
                $order->save();
                $tab = 7;
           // }
        

        $getUserOrder = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->select('users.email', 'orders.order_id', 'orders.notes')
            ->where('orders.id', $id)->first();


        // Send email notification
        $getUserOrder->notify(new OrderCancelledNotification($getUserOrder));

        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Cancel',
            'activity_description' => 'Cancelled order id' . ' ' . $id,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);


        return response()->json(['message' => 'Order Cancelled', 'tab' => $tab], 200);
    }


}