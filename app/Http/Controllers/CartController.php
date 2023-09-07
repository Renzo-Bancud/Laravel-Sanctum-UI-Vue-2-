<?php

namespace App\Http\Controllers;


use App\Models\Activitylog;
use App\Models\Cart;
use App\Models\Cart_item;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\User;
use App\Models\User_contact;
use App\Models\Rating;
use App\Models\Inventory;
use App\Models\Variation_option;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderisPlaced;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Notifications\DeliveryNotifications;



class CartController extends Controller
{

    protected $dateTime;

    public function __construct()
    {
        $now = Carbon::now('Asia/Manila');
        $this->dateTime = $now->toDateTimeString();
    }

    public function my_cart(Request $request)
    {
        $user = $request->user();
        $cartItem = $request->input('item');
        $deliveryID = $request->input('delivery_id');

        $existingItem = Cart_item::join('carts', 'cart_items.cart_id', 'carts.id')
            ->where('carts.user_id', $user->id)
            ->where('cart_items.product_id', $cartItem)
            ->where('cart_items.delivery_id', $deliveryID)
            ->first();


        if ($existingItem) {
            $existingItem->qty_cart += 1;
            $existingItem->updated_at = $this->dateTime;
            $existingItem->save();
        } else {
            $cart = Cart::create(['user_id' => $user->id]);

            if ($cart) {
                $lastInsertedId = $cart->id;

                Cart_item::create([
                    'cart_id' => $lastInsertedId,
                    'product_id' => $cartItem,
                    'delivery_id' => $deliveryID,
                    'qty_cart' => 1,
                    'created_at' => $this->dateTime,
                    'updated_at' => $this->dateTime,
                ]);
            }
        }

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Add Cart',
            'activity_description' => 'Add cart items',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        return response()->json([
            'message' => 'Cart item saved successfully.',
        ], 200)->header('Retry-After', 60); // Set the retry-after time in seconds (e.g., 60 seconds).
    }

    public function load_my_cart()
    {
        $uid = auth::user()->id;

        // Query to get your cart items
        $your_cart = Cart_item::where('carts.user_id', $uid)
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->orderBy('cart_items.id', 'DESC')
            ->limit(5)
            ->get();

        $search = request('search', null);

        // Query to check for the latest updates in the user's cart
        $latestUpdate = Cart::where('user_id', $uid)->get();
        $getVariationOptions = Variation_option::get();

        $query = Cart::where('carts.user_id', $uid)
            ->join('cart_items', 'carts.id', '=', 'cart_items.cart_id')
             ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->join('inventories', 'cart_items.delivery_id', '=', 'inventories.delivery_id')
            ->join('product_deliveries', 'cart_items.delivery_id', '=', 'product_deliveries.id')
            ->select(
                'carts.id as cart_id',
                'cart_items.id as item_id',
                'cart_items.updated_at as cartitems_updated_at',
                'cart_items.*',
                 'products.*',
                 'inventories.qty_in',
                 'inventories.qty_out',
                 'product_deliveries.id as delivery_id',
                 'product_deliveries.variation_option_id'
            );

        // Check if latest updates exist and apply relevant query conditions
        if ($latestUpdate->count() > 0 && (empty($latestUpdate[0]) || $latestUpdate[0]->updated_at == $this->dateTime || $this->dateTime > $latestUpdate[0]->updated_at)) {
            $query->orderBy('cart_items.updated_at', 'DESC');
        } else {
            $query->orderBy('carts.id', 'DESC');
        }

        // Apply search filter if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%");
            });
        }

        // Retrieve the cart items based on the constructed query
        $cartItems = $query->get();

        //dd($cartItems);

        // Count the total number of all items
        $totalItems = count($cartItems);

        // Deduct the limited query result of 5 items
        $remainingItems = max(0, $totalItems - 5);

        // Return the response based on the retrieved data
        if ($your_cart->isEmpty() && $cartItems->isEmpty()) {
            return response()->json([
                'MyCartitems' => [],
                'your_cart' => [],
                'remainingItems' => 0,
                'getVariationOptions' => $getVariationOptions,
            ], 200);
        }

        return response()->json([
            'MyCartitems' => $cartItems,
            'your_cart' => $your_cart,
            'remainingItems' => $remainingItems,
            'getVariationOptions' => $getVariationOptions,
        ], 200);
    }

    public function remove_item($cart_id)
    {
        $cart = Cart_item::find($cart_id);
        $isdeleted = $cart->delete();
        if ($isdeleted) {
            Cart::where('id', $cart->cart_id)->delete();
        }

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Trash',
            'activity_description' => 'Remove item',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        return response()->json(['success' => 'Item deleted.']);
    }


    public function remove_pending_item($order_id)
    {
        $order = Order::where('status', 0)->find($order_id);
        $isdeleted = $order->delete();
        if ($isdeleted) {
            Order_item::where('order_id', $order_id)->delete();
        }

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Trash',
            'activity_description' => 'Remove Pending item',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        return response()->json(['success' => 'Order deleted.']);
    }



    public function remove_selected_item($cart_item_id)
    {
        $cart = Cart_item::find($cart_item_id);
        $cart->delete();
        Cart::where('id', $cart->cart_id)->update(['status' => 0]);

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Trash',
            'activity_description' => 'Remove unselected items',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        return response()->json(['success' => 'Item deleted.']);
    }


    public function proceed_to_checkout(Request $request)
    {
        $selectedItems = $request->input('selectedItems');
        $uid = auth::user()->id;

        $totalAmount = 0;
        $ordersCreated = 0;

        foreach ($selectedItems as $selectedItem) {
            $qtyCart = $selectedItem['qty_cart'];
            $price = $selectedItem['price'];
            $onsale = $selectedItem['isOnSale'];
            $discountPercent = $selectedItem['discount_percentage'];
            $discount = $price * ($discountPercent / 100);
            $saleprice = $price - $discount;
            $finalPrice = $onsale == null ? $price : $saleprice;
            $fee = $selectedItem['shipping_fee'];
            $prodIds = $selectedItem['prod_id'];
            $deliveryId = $selectedItem['delivery_id'];

            $totalAmount = $finalPrice * $qtyCart + $fee;

            // Check if the selected item already has an existing order with status 0 or 1
            $existingOrderItem = Order_item::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.user_id', $uid)
                ->whereIn('order_items.product_id', [$prodIds])
                ->whereIn('order_items.delivery_id', [$deliveryId])
                ->whereIn('orders.status', [0, 1])
                ->get();



            if ($existingOrderItem->isEmpty()) {
                // Create a new order
                $order = new Order();
                $orderID = $order->generateOrderID();

                $create_order = Order::create([
                    'user_id' => $uid,
                    'order_id' => $orderID,
                    'cart_item_id' => $selectedItem['cart_item_id'],
                    'total_amount' => $totalAmount,
                    'created_at' => $this->dateTime,
                    'updated_at' => $this->dateTime,
                ]);

                if ($create_order) {
                    // Get the ID of the created order
                    $lastInsertedId = $create_order->id;

                    // Insert the item associated with the order
                    Order_item::create([
                        'order_id' => $lastInsertedId,
                        'product_id' => $selectedItem['prod_id'],
                        'delivery_id' => $deliveryId,
                        'qty' => $qtyCart,
                        'price' => $price,
                        'created_at' => $this->dateTime,
                        'updated_at' => $this->dateTime,
                    ]);

                    $ordersCreated++;
                }
            }
        }

        if ($ordersCreated > 0) {

            $getUserOrder = User::where('users.id', $uid)
                ->join('orders', 'users.id', '=', 'orders.user_id')
                ->select('users.email', 'orders.total_amount', 'orders.order_id')
                ->first();

            $getUserOrder->notify(new OrderisPlaced($getUserOrder));
            $message = 'Place order successfully.';
        } else {
            // Check each $existingOrderItem if there is any and show $message
            $message = 'Exist';
        }

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Checkout',
            'activity_description' => 'Proceed to checkout',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        // Return a response indicating the success or failure of the operation
        return response()->json(['message' => $message]);
    }





    // public function proceed_to_checkout(Request $request)
    // {
    //     // Get the selected items from the request
    //     $selectedItems = $request->input('selectedItems');

    //     // Single extract param the IDs of the selected items method
    //     // $selectedItemIds = array_map(function ($selectedItem) {
    //     //     return $selectedItem['id'];
    //     // }, $selectedItems);

    //     //Multiple extract param
    //     // $selectedItemsData = array_map(function ($selectedItem) {
    //     //     return [
    //     //         'id' => $selectedItem['id'],
    //     //         'name' => $selectedItem['name'],
    //     //     ];
    //     // }, $selectedItems);

    //     // Extract the IDs into a separate array
    //     // $selectedItemIds = array_column($selectedItemsData, 'id');

    //     // Extract the names into a separate array
    //     // $selectedItemNames = array_column($selectedItemsData, 'name')

    //     foreach ($selectedItems as $selectedItem) {
    //         $product_id = $selectedItem['prod_id'];
    //         $qtyCart = $selectedItem['qty_cart'];
    //         $cart_item_id = $selectedItem['cart_item_id'];
    //         $price = $selectedItem['price'];
    //         $onsale = $selectedItem['isOnSale'];
    //         $discountPercent = $selectedItem['discount_percentage'];
    //         $discount = $price * ($discountPercent / 100);
    //         $saleprice = $price - $discount;
    //         $finalPrice = $onsale == null ? $price : $saleprice;
    //         $fee = $selectedItem['shipping_fee'];
    //         $uid = auth::user()->id;



    //         // Create an instance of the Order model
    //         $order = new Order();

    //         // Call the generateOrderID() function
    //         $orderID = $order->generateOrderID();


    //         //track item
    //         $trackExistOrderItems = Order_item::join('orders', 'order_items.order_id', '=', 'orders.id')
    //             ->select('order_items.*')
    //             ->where('orders.user_id', $uid)
    //             ->where('order_items.product_id', $product_id)
    //             ->whereIn('orders.status', [0, 1])
    //             ->orderBy('orders.id', 'DESC')
    //             ->get();

    //         if ($trackExistOrderItems->isEmpty()) {

    //             // how can i insert two item i selected is two
    //             $create_order = Order::create([
    //                 'user_id' => $uid,
    //                 'order_id' => $orderID,
    //                 'cart_item_id' => $cart_item_id,
    //                 'total_amount' => $finalPrice * $qtyCart + $fee,
    //                 'created_at' => $this->dateTime,
    //                 'updated_at' => $this->dateTime,
    //             ]);

    //             $lastInsertedId = $create_order->id;

    //             if ($create_order) {
    //                 Order_item::create([
    //                     'order_id' => $lastInsertedId,
    //                     'product_id' => $product_id,
    //                     'qty' => $qtyCart,
    //                     'price' => $price,
    //                     'created_at' => $this->dateTime,
    //                     'updated_at' => $this->dateTime,
    //                 ]);
    //             }

    //             $message = 'Place order successfully';
    //         } else {
    //             $message = 'Exist';
    //         }

    //     }

    //     Activitylog::create([
    //         'user_id' => auth::user()->id,
    //         'activity_type' => 'Checkout',
    //         'activity_description' => 'Proceed to checkout',
    //         'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    //         'ip_address' => $_SERVER['REMOTE_ADDR'],
    //         'created_at' => $this->dateTime,
    //         'updated_at' => $this->dateTime,
    //     ]);

    //     // Return a response indicating the success or failure of the operation
    //     return response()->json(['message' => $message]);
    // }

    public function track_order_status(Request $request)
    {
        $uid = auth::user()->id;
        $prodIds = $request->input('prod_id');

        $track_items = Order_item::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $uid)
            ->whereIn('order_items.product_id', $prodIds)
            ->whereIn('orders.status', [0, 1])
            ->orderBy('orders.id', 'DESC')
            ->get();

        return response()->json(['track_items' => $track_items], 200);
    }


    public function load_ordered_item()
    {
        $uid = auth::user()->id;

        $query = Order_item::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_deliveries', 'order_items.delivery_id', '=', 'product_deliveries.id')
            ->select('order_items.id as order_item_id', 'order_items.*', 'orders.id as order_id', 'orders.order_id as order_track_id',
            'products.id as prod_id', 'products.*', 'product_deliveries.variation_option_id')
            ->where('orders.status', 0)
            ->where('orders.user_id', $uid)->get();

        $query->each(function ($item) {
            $variationOptionIds = explode(',', $item->variation_option_id);
            $variationOptions = Variation_option::whereIn('id', $variationOptionIds)->get();
            $item->variation_options = $variationOptions;
        });

        // Get the cart items from the query result
        $selectedItems = $query;

        return response()->json([
            'selectedItems' => $selectedItems,
        ], 200);
    }

    public function update_payment_type(Request $request)
    {
        $orders = $request->input('orders');
        $paymentType = $request->input('paymentType');


        foreach ($orders as $order) {
            $orderIds = [$order]; // Assuming $order is the order ID
            $orderItems = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->whereIn('orders.id', $orderIds)
                ->where('orders.status', 0)
                ->get();

            foreach ($orderItems as $orderItem) {

                $getUserOrder = User::where('users.id', $orderItem->user_id)
                    ->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('users.email', 'orders.total_amount', 'orders.order_id')
                    ->first();

                if ($paymentType === 'cod') {
                    $getUserOrder->notify(new DeliveryNotifications($getUserOrder));

                    // $productIds = [$orderItem['product_id']]; // Convert to array
                    // $inventoryItems = Inventory::whereIn('product_id', $productIds)->get();

                    // foreach ($inventoryItems as $inventoryItem) {
                    //     $inventoryItem->update([
                    //         'qty_out' => $inventoryItem->qty_out + $orderItem['qty'],
                    //         'updated_at' => $this->dateTime,
                    //     ]);
                    // }


                    $getCartItems = Cart_item::whereIn('id', [$orderItem->cart_item_id])->get();
                    foreach ($getCartItems as $cartItem) {
                        Cart::whereIn('id', [$cartItem->cart_id])->delete();
                    }
                }
            }


            $uid = auth::user()->id;
            $address = User_contact::where('user_id', $uid)->where('isCurrentLocation', 1)->first();


            // Update status and other order-level operations here
            Order::whereIn('id', $orderIds)->update([
                'shipping_address' => $address->id,
                'payment_method' => $paymentType,
                'status' => 1,
                'updated_at' => $this->dateTime, // Other fields to update for the order
            ]);
        }

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Update',
            'activity_description' => 'Payment type update to COD',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        // Return a response indicating success
        return response()->json(['message' => 'Order is on Process']);
    }

    public function placed_order(Request $request, $uid)
    {
        $orderTrackId = $request->query('orderTrackId');
        $status = ['1', '2', '3', '4'];

        $cart_status = Order::where('user_id', $uid)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('inventories', 'order_items.delivery_id', '=', 'inventories.delivery_id')
            ->select(
                'orders.id as orders_id',
                'orders.*',
                'order_items.id as order_items_id',
                'order_items.*',
                'products.id as prod_id',
                'products.*',
                'inventories.qty_in',
                'orders.created_at as order_date_created',
                'orders.updated_at as order_date_updated'
            )
            ->where('orders.order_id', $orderTrackId)
            ->whereIn('orders.status', $status)
            ->get();

        return response()->json($cart_status, 200);
    }

    public function placed_order_details(Request $request, $uid)
    {
        $slug = $request->query('slug');
        $order_id = crypt::decrypt($request->query('order_id'));


        $cart_status = Order::where('user_id', $uid)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('inventories', 'order_items.delivery_id', '=', 'inventories.delivery_id')
            ->select('orders.id as orders_id', 'orders.*', 'order_items.id as order_items_id', 'order_items.*', 'products.id as prod_id', 'products.*', 'inventories.qty_in', 'orders.created_at as order_date_created', 'orders.updated_at as order_date_updated')
            ->where('orders.id', $order_id)
            //->where('products.slug', $slug)
            ->get();

        return response()->json($cart_status, 200);
    }

    public function get_items_status(Request $request, $uid)
    {
        $orderTrackId = $request->query('orderTrackId');
        $status = ['1', '2', '3', '4'];

        $cart_status = Order::where('user_id', $uid)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('inventories', 'order_items.delivery_id', '=', 'inventories.delivery_id')
            ->select('orders.id as order_id', 'orders.*', 'order_items.id as order_items_id', 'order_items.*', 'products.id as prod_id', 'products.*', 'inventories.qty_in', 'orders.created_at as order_date_created', 'orders.updated_at as order_date_updated')
            ->groupBy('orders.payment_method')
            ->where('orders.order_id', $orderTrackId)
            ->whereIn('orders.status', $status)
            ->get();

        return response()->json($cart_status, 200);
    }

    public function get_items_status_details(Request $request, $uid)
    {
        $slug = $request->query('slug');
        $order_id = crypt::decrypt($request->query('order_id'));


        $cart_status = Order::where('user_id', $uid)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('inventories', 'order_items.delivery_id', '=', 'inventories.delivery_id')
            ->select('orders.id as order_id', 'orders.updated_at as orders_updated_at', 'orders.*', 'order_items.id as order_items_id', 'order_items.*', 'products.id as prod_id', 'products.*', 'inventories.qty_in', 'orders.created_at as order_date_created', 'orders.updated_at as order_date_updated')
            ->groupBy('orders.payment_method')
            ->where('orders.id', $order_id)
            //->where('products.slug', $slug)
            ->get();

        return response()->json($cart_status, 200);
    }


    public function cancel_item(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'notes' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');
        $notes = $request->input('notes');
        // $find_cart = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')->where('orders.id', $id)->first();

        // $find_inventory = Inventory::where('delivery_id', $find_cart->delivery_id)->first();
        // $find_inventory->update(['qty_out' => $find_inventory->qty_out - $find_cart->qty]);
        // if ($find_inventory) {


            $order = Order::find($id);
            $order->status = 7;
            $order->cancel_by = 1;
            $order->notes = $notes;
            $order->updated_at = $this->dateTime;
            $order->save();
        //}


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
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);


        return response()->json(['message' => 'Order Cancelled'], 200);
    }


    public function purchase_orders(Request $request, $uid)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        if ($status !== 0) {
            $query = Order::where('user_id', $uid)
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('inventories', 'order_items.delivery_id', '=', 'inventories.delivery_id')
                ->join('product_deliveries', 'order_items.delivery_id', '=', 'product_deliveries.id')
                ->select('orders.id as order_id', 'orders.*', 'order_items.id as order_items_id', 'order_items.*', 'orders.order_id as order_track_id', 'products.id as prod_id',
                'products.*', 'inventories.qty_in', 'categories.name','product_deliveries.variation_option_id')
                ->orderByRaw('CASE WHEN orders.status = 1 THEN 0 ELSE 1 END ASC')
                ->orderBy('orders.status', 'DESC');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('products.title', 'LIKE', "%$search%");
                });
            }

            $user_carts = $query->get();
        } else {
            $query = Order::where('user_id', $uid)
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('inventories', 'order_items.delivery_id', '=', 'inventories.delivery_id')
                ->join('product_deliveries', 'order_items.delivery_id', '=', 'product_deliveries.id')
                ->select('orders.id as order_id', 'orders.*', 'order_items.id as order_items_id', 'order_items.*', 'orders.order_id as order_track_id',
                'products.id as prod_id', 'products.*', 'inventories.qty_in', 'categories.name','product_deliveries.variation_option_id')
                ->orderByRaw('CASE WHEN orders.status = 1 THEN 0 ELSE 1 END ASC')
                ->orderBy('orders.status', 'DESC');

                // Get the cart items from the query result
                $user_carts = $query->get();

        }

        // Generate unique order_id values
        // $user_carts = $user_carts->map(function ($cart) {
        //     $cart->order_id = uniqid(); // Generate a unique identifier
        //     return $cart;
        // });

        if ($user_carts->count() === 0) {
            return response()->json(['message' => 'No data available.'], 200);
        }

        return response()->json(['user_carts' => $user_carts], 200);
    }


    public function save_rating(Request $request)
    {

        $order_id = $request->input('selectedId');
        $rating = $request->input('rateItemValue');



        $validator = Validator::make($request->all(), [
            'feedback' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $save = Rating::create([
            'user_id' => auth::user()->id,
            'order_id' => $order_id,
            'rate_star' => $rating,
            'feedback' => $request->feedback,
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        if ($save) {
            Order::where('id', $order_id)->update(['status' => 6, 'updated_at' => $this->dateTime]);
        }

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Save rating',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);


        return response()->json(['message' => 'Your rating is submitted'], 200);
    }
}
