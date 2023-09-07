<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
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

        $query = Product::query()->with(['category', 'variations', 'variations.options']);

        if ($search) {
            $query->select('products.id', 'products.*', 'categories.name')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where(function ($q) use ($search) {
                    $q->where('categories.name', 'LIKE', "%$search%")
                        ->orWhere('title', 'LIKE', "%$search%")
                        ->orWhere('price', 'LIKE', "%$search%")
                        ->orWhere('products.created_at', 'LIKE', "%$search%");
                });
        }

        $products = $query->latest('products.created_at')->paginate($paginate);


        return response()->json($products, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255|unique:products',
            'images.*' => 'required|image|max:2048',
            'price' => 'required',
            'fee' => 'required',
            'description' => 'required',
            'category' => 'required',
            'main_picture_index' => 'required', // Add validation for main picture index
        ], [
            'title.unique' => 'Craft Unique Title to Prevent Confusion.',
            'main_picture_index.required' => 'Please select atleast main picture of your product.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $product = Product::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'price' => $request->price,
            'discount_percentage' => empty($request->discount) ? null : $request->discount,
            'isOnSale' => empty($request->discount) ? null : 1,
            'shipping_fee' => $request->fee,
            'description' => $request->description,
            'category_id' => $request->category,
            'created_at' => Carbon::now('Asia/Manila'),
            'updated_at' => Carbon::now('Asia/Manila'),
        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $index => $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('storage/product'), $imageName);
                $imagePaths[] = '/storage/product/' . $imageName;

                // Check if the current index matches the main picture index
                if ($index === (int) $request->main_picture_index) {
                    $product->main_picture = '/storage/product/' . $imageName;
                }
            }
            $product->image = implode(',', $imagePaths);
            $product->save();
        }

        // Get the selected variations from the request
        $selectedVariations = json_decode($request->selectedVariations, true);

        if (!empty($selectedVariations)) {
            foreach ($selectedVariations as $variation) {
                $variationId = $variation['variation_id'];
                $optionIds = implode(',', $variation['option_ids']);

                if ($optionIds) { // Skip null option IDs (if no option selected)
                    $product->variations()->attach($variationId, ['variation_option_id' => $optionIds]);
                }
            }
        }


        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Create product',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Product added'], 200);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product): JsonResponse
    {
        if ($product) {
            return response()->json($product, 200);
        } else {
            return response()->json('Failed', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255|unique:products,title,' . $product->id,
            'images.*' => 'sometimes|nullable|image|max:2048',
            'price' => 'required',
            'fee' => 'required',
            'description' => 'required',
            'category' => 'required',
            'main_picture_index' => 'nullable|integer', // Add validation for main picture index
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Store the old image paths
        $oldImagePaths = explode(',', $product->image);

        $product->title = $request->title;
        $product->slug = Str::slug($request->title);
        $product->price = $request->price;
        $product->discount_percentage = empty($request->percentage) ? null : $request->percentage;
        $product->isOnSale = empty($request->percentage) ? null : 1;
        $product->shipping_fee = $request->fee;
        $product->description = $request->description;
        $product->category_id = $request->category;
        $product->updated_at = Carbon::now('Asia/Manila');

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $index => $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('storage/product'), $imageName);
                $imagePaths[] = '/storage/product/' . $imageName;

                if ((int) $index === (int) $request->main_picture_index) {
                    $product->main_picture = '/storage/product/' . $imageName;
                }
            }
            $product->image = implode(',', $imagePaths);

            // Unlink the old image files
            foreach ($oldImagePaths as $oldImagePath) {
                $oldImagePath = public_path($oldImagePath);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }


        $product->save();

        // Handle variations
        if ($request->has('selectedVariations')) {
            $selectedVariations = $request->selectedVariations;

            // Detach all old variations before attaching the updated ones
            $product->variations()->detach();

            foreach ($selectedVariations as $variation) {
                $variationId = $variation['variation_id'];
                $optionIds = implode(',', $variation['option_ids']);

                if ($optionIds) { // Skip null option IDs (if no option selected)
                    $product->variations()->attach($variationId, ['variation_option_id' => $optionIds]);
                }
            }
        }

        // Fetch the variations after update
        $attachedVariations = $product->variations()->with('options')->get();


        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Update',
            'activity_description' => 'Update product',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Product updated', 'variations' => $attachedVariations], 200); // i want to return response the attach prodcut_variations
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        if ($product) {
            // Check and delete the main picture
            $mainImagePath = public_path($product->main_picture);
            if (file_exists($mainImagePath)) {
                unlink($mainImagePath);
            }

            // Check and delete the product images
            $imagePaths = explode(',', $product->image);
            foreach ($imagePaths as $imagePath) {
                $imageFullPath = public_path($imagePath);
                if (file_exists($imageFullPath)) {
                    unlink($imageFullPath);
                }
            }

            // Detach related variations from the pivot table
            $product->variations()->detach();

            // Delete the product data from the database
            $product->delete();

            Activitylog::create([
                'user_id' => Auth::user()->id,
                'activity_type' => 'Trash',
                'activity_description' => 'Delete product',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ]);

            return response()->json('success', 200);
        } else {
            return response()->json('Failed', 404);
        }
    }
}
