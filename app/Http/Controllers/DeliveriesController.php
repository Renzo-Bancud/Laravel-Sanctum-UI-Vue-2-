<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Variation_option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\Product_delivery;
use Illuminate\Support\Facades\DB;


class DeliveriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $paginate = request('paginate', 10);
        $search = request('search', null);

        $query = Product_delivery::query()->with('product');

        $product_id = request('product_id');

        // Call the custom function to get the product variations with their variations and options
        $product_variations = $this->getProductVariations($product_id);


        if ($search) {
            $query->select('product_deliveries.id', 'product_deliveries.*', 'products.id as product_id', 'products.*')
                ->join('products', 'product_deliveries.product_id', '=', 'products.id')
                ->where(function ($q) use ($search) {
                    $q->where('products.title', 'LIKE', "%$search%")
                        ->orWhere('product_deliveries.created_at', 'LIKE', "%$search%");
                });
        }

        $product_deliveries = $query->latest('product_deliveries.created_at')->paginate($paginate);

        // Process variation names and variation option values
        foreach ($product_deliveries as $delivery) {
            $variationIds = explode(',', $delivery->variation_id);
            $variationOptionIds = explode(',', $delivery->variation_option_id);

            // Fetch variations based on variationIds
            $variations = Variation::whereIn('id', $variationIds)->pluck('variation_name', 'id');
            $delivery->variations = $variations->toArray();

            // Fetch variation options based on variationOptionIds
            $options = Variation_option::whereIn('id', $variationOptionIds)->pluck('value', 'id');
            $delivery->options = $options->toArray();
        }



        return response()->json(['product_deliveries' => $product_deliveries, 'product_variations' => $product_variations], 200);
    }

    public function getProductVariations($product_id)
    {
        // Get the product_variations for the given product_id
        $product_variations = DB::table('product_variation')
            ->where('product_id', $product_id)
            ->get();

        // Loop through each product variation and process the variation_option_id
        foreach ($product_variations as $product_variation) {
            // Split the variation_option_id string into an array of option IDs
            $option_ids = explode(',', $product_variation->variation_option_id);

            // Get the Variation and its associated options based on the option IDs
            $variation = Variation::with('options')->find($product_variation->variation_id);
            $options = Variation_option::whereIn('id', $option_ids)->get();

            // Add the Variation and its associated options to the product_variation object
            $product_variation->variation = $variation;
            $product_variation->options = $options;
        }

        return $product_variations;
    }

    public function product_selection(): JsonResponse
    {
        $products = Product::latest()->get();
        return response()->json($products, 200);
    }

    public function product_inventory(): JsonResponse
    {
        $paginate = request('paginate', 10);
        $search = request('search', null);

        $query = Inventory::query()->with('product');

        if ($search) {
            $query->select('inventories.id', 'inventories.*', 'products.title')
                ->join('products', 'inventories.product_id', '=', 'products.id')
                ->join('cart_items', 'inventories.product_id', '=', 'cart_items.product_id')
                ->where(function ($q) use ($search) {
                    $q->where('products.title', 'LIKE', "%$search%");
                });
        }

        $inventories = $query->paginate($paginate);

        return response()->json($inventories, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required',
            'selectedVariations' => 'required',
            'qty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $variationIds = $request->input('variationIds');
        $optionIds = $request->input('optionIds');

        $variationIdsString = implode(', ', $variationIds);
        $optionIdsString = implode(', ', $optionIds);

        $delivery = Product_delivery::where('product_id', $request->product)
            ->where('variation_id', $variationIdsString)
            ->where('variation_option_id', $optionIdsString)
            ->first();


        if ($delivery) {
            $delivery->update([
                'quantity' => DB::raw('quantity + ' . $request->qty),
            ]);

            Inventory::where('delivery_id', $delivery->id)->update([
                'qty_in' => DB::raw('qty_in + ' . $request->qty)
            ]);

        } else {
            $create = Product_delivery::create([
                'product_id' => $request->product,
                'variation_id' => $variationIdsString,
                'variation_option_id' => $optionIdsString,
                'quantity' => $request->qty,
                'supplier_id' => 0,
                'warehouse_id' => 0,
                'employee_id' => 0,
                'notes' => $request->notes,
            ]);

            Inventory::create(
                [
                    'product_id' => $request->product,
                    'delivery_id' => $create->id,
                    'qty_in' => DB::raw('qty_in + ' . $request->qty)
                ]
            );
        }




        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Create delivery',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Delivery added'], 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product_delivery $product_delivery): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required',
            'qty_action' => 'required',
        ], [
            'action.required' => 'The type of action is required.',
            'qty_action.required' => 'The specify qty is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->action == 1) {

            $product_delivery->update([
                'quantity' => DB::raw('quantity + ' . $request->qty_action),
                'status' => $request->action
            ]);

            Inventory::where('delivery_id', $product_delivery->id)->update([
                'qty_in' => DB::raw('qty_in + ' . $request->qty_action)
            ]);

        } else {

            $product_delivery->update([
                'quantity' => DB::raw('quantity - ' . $request->qty_action),
            ]);

            Inventory::where('delivery_id', $product_delivery->id)->update([
                'qty_in' => DB::raw('qty_in - ' . $request->qty_action)
            ]);
        }

        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Edit',
            'activity_description' => 'Update the delivery id' . ' ' . $product_delivery->id,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Delivery updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {

        $product = Product_delivery::find($id);
        $product->delete();
        Inventory::where('delivery_id', $id)->delete();


        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Delete',
            'activity_description' => 'The delivered item has been removed from the inventory.',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);


        return response()->json(['message' => 'Product successfully removed'], 200);
    }
}