<?php

namespace App\Http\Controllers;


use App\Models\Activitylog;
use App\Models\Product;
use App\Models\Product_delivery;
use App\Models\Subscription;
use App\Models\Company_detail;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Cart_item;
use App\Models\Rating;
use App\Models\Slogan;
use App\Models\Variation;
use App\Models\Variation_option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PublicAPIController extends Controller
{
  public function products()
  {
    $paginate = request('paginate', 10);
    $search = request('search', null);
    $category = request('category', null);

    $query = Product::query()->with('category');

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('title', 'LIKE', "%$search%")
          ->orWhere('price', 'LIKE', "%$search%")
          ->orWhere('description', 'LIKE', "%$search%")
          ->orWhere('created_at', 'LIKE', "%$search%");
      });
    }

    if ($category) {
      $query->where(function ($q) use ($category) {
        $q->where('category_id', 'LIKE', "%$category%");
      });
    }

    $products = $query->latest('created_at')->paginate($paginate);

    return response()->json($products, 200);
  }


  public function sale_products()
  {
    $paginate = request('paginate', 10);
    $search = request('search', null);
    $category = request('category', null);

    $query = Product::query()->with('category')->where('isOnSale', 1);

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('title', 'LIKE', "%$search%");
      });
    }

    if ($category) {
      $query->where(function ($q) use ($category) {
        $q->where('category_id', 'LIKE', "%$category%");
      });
    }

    $products = $query->latest('created_at')->paginate($paginate);

    return response()->json($products, 200);
  }


  public function load_category_products()
  {
    $paginate = request('paginate', 10);
    $search = request('search', null);
    $category = request('category', null);

    $category_id = Category::where('slug', $category)->first();

    if ($category_id !== null) {
      $query = Product::query()->with('category')->where('category_id', $category_id->id);
      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('title', 'LIKE', "%$search%");
        });
      }

      $products = $query->latest('created_at')->paginate($paginate);
      return response()->json($products, 200);
    } else {
      return response()->json(['message', 'No product found']);
    }
  }


  public function product_categories()
  {
    $categories = Category::latest()->get();
    return response()->json($categories, 200);
  }


  public function product_details($slug)
  {
    $product = Product::with(['category'])
      ->leftJoin('inventories', 'products.id', '=', 'inventories.product_id')
      ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
      ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
      ->leftJoin('cart_items', 'products.id', '=', 'cart_items.product_id')
      ->select('products.id', 'products.*', 'inventories.*', 'order_items.qty', 'orders.status', 'cart_items.qty_cart')
      ->where('products.slug', $slug)
      ->first();

    // Fetch product deliveries and join the 'products' table to get product details
    $Productdeliveries = Product_delivery::join('products', 'product_deliveries.product_id', 'products.id')
      ->join('inventories', 'product_deliveries.id', 'inventories.delivery_id')
      ->select('product_deliveries.id as delivery_id', 'product_deliveries.*', 'inventories.qty_in', 'inventories.qty_out')
      ->where('products.slug', $slug)
      ->get();

    // Initialize empty arrays to store grouped variations and options
    $groupedVariations = [];
    $groupedOptions = [];

    // Loop through each product delivery
    foreach ($Productdeliveries as $delivery) {
      // Split the comma-separated variation IDs and option IDs into arrays
      $variationIds = explode(',', $delivery->variation_id);
      $variationOptionIds = explode(',', $delivery->variation_option_id);


      // Group variations and variation options
      foreach ($variationIds as $index => $variationId) {

        $inventoryInQuantity = $delivery->qty_in;
        $inventoryOutQuantity = $delivery->qty_out;
        $DeliveryId = $delivery->delivery_id;

        // Get the corresponding option ID at the same index
        $optionId = $variationOptionIds[$index] ?? null;
        if ($variationId && $optionId) {
          // Fetch variation name from the 'Variation' model by the variation ID
          $variationName = Variation::where('id', $variationId)->value('variation_name');
          if (!isset($groupedVariations[$variationId])) {
            // Store the variation name in the groupedVariations array with the variation ID as the key
            $groupedVariations[$variationId] = $variationName;
          }

          // Fetch option value from the 'Variation_option' model by the option ID
          $optionValue = Variation_option::where('id', $optionId)->value('value');
          if (!isset($groupedOptions[$variationId])) {
            // Initialize an empty array for the variation ID if it doesn't exist
            $groupedOptions[$variationId] = [];
          }

          // Check if the option already exists in the groupedOptions array
          $optionIndex = -1;
          foreach ($groupedOptions[$variationId] as $idx => $opt) {
            if ($opt['option'] === $optionValue) {
              $optionIndex = $idx;
              break;
            }
          }

          // If the option already exists, skip adding it again
          if ($optionIndex !== -1) {
            continue;
          }

          // Add the option to the groupedOptions array
          $firstOptionId = $variationOptionIds[0];
          $groupedOptions[$variationId][] = [
            'option' => $optionValue,
            'option_id' => $firstOptionId,
          ];
        }
      }

      

      // Update the last option's 'qty_in' and 'delivery_id'
      if (!empty($groupedOptions)) {
        $lastVariationId = array_key_last($groupedOptions);
        $lastOptionIndex = count($groupedOptions[$lastVariationId]) - 1;
        $groupedOptions[$lastVariationId][$lastOptionIndex]['qty_in'] = $inventoryInQuantity;
        $groupedOptions[$lastVariationId][$lastOptionIndex]['qty_out'] = $inventoryOutQuantity; // why this is null even there is group that is not null
        $groupedOptions[$lastVariationId][$lastOptionIndex]['delivery_id'] = $DeliveryId;
      }
    }

    if ($product && $Productdeliveries) {
      // Combine the data into a single array
      $responseData = [
        'product' => $product->toArray(),
        'groupedVariations' => $groupedVariations,
        'groupedOptions' => $groupedOptions,
      ];

      // Add this line to check the contents of $responseData
     // dd($responseData);

      return response()->json($responseData, 200);
    } else {

      return response()->json('not found', 404);
    }
  }



  // public function product_details($slug)
  // {
  //   $product = Product::with(['category'])
  //     ->leftJoin('inventories', 'products.id', '=', 'inventories.product_id')
  //     ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
  //     ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
  //     ->leftJoin('cart_items', 'products.id', '=', 'cart_items.product_id')
  //     ->select('products.id', 'products.*', 'inventories.*', 'order_items.qty', 'orders.status', 'cart_items.qty_cart')
  //     ->where('products.slug', $slug)
  //     ->first();

  //   // Fetch product deliveries and join the 'products' table to get product details
  //   $Productdeliveries = Product_delivery::join('products', 'product_deliveries.product_id', 'products.id')
  //     ->join('inventories', 'product_deliveries.id', 'inventories.delivery_id')
  //     ->select('product_deliveries.id as delivery_id', 'product_deliveries.*', 'inventories.qty_in')
  //     ->where('products.slug', $slug)
  //     ->get();

  //   // Initialize empty arrays to store grouped variations and options
  //   $groupedVariations = [];
  //   $groupedOptions = [];

  //   // Loop through each product delivery
  //   foreach ($Productdeliveries as $delivery) {
  //     // Split the comma-separated variation IDs and option IDs into arrays
  //     $variationIds = explode(',', $delivery->variation_id);
  //     $variationOptionIds = explode(',', $delivery->variation_option_id);

  //     // Group variations and variation options
  //     foreach ($variationIds as $index => $variationId) {
  //       // Get the corresponding option ID at the same index
  //       $optionId = $variationOptionIds[$index] ?? null;
  //       if ($variationId && $optionId) {
  //         // Fetch variation name from the 'Variation' model by the variation ID
  //         $variationName = Variation::where('id', $variationId)->value('variation_name');
  //         if (!isset($groupedVariations[$variationId])) {
  //           // Store the variation name in the groupedVariations array with the variation ID as the key
  //           $groupedVariations[$variationId] = $variationName;
  //         }

  //         // Fetch option value from the 'Variation_option' model by the option ID
  //         $optionValue = Variation_option::where('id', $optionId)->value('value');
  //         if (!isset($groupedOptions[$variationId])) {
  //           // Initialize an empty array for the variation ID if it doesn't exist
  //           $groupedOptions[$variationId] = [];
  //         }

  //         // Add the option value and qty_in to the groupedOptions array for the corresponding variation ID
  //         $inventoryInQuantity = $delivery->qty_in;
  //         $groupedOptions[$variationId][] = [
  //           'id' => $optionId,
  //           'value' => $optionValue,
  //           'qty_in' => $inventoryInQuantity,
  //           'delivery_id' => $delivery->delivery_id ?? null, // we set null if id not exist
  //         ];
  //       }
  //     }
  //   }

  //   if ($product && $Productdeliveries) {
  //     // Combine the data into a single array
  //     $responseData = [
  //       'product' => $product->toArray(),
  //       'groupedVariations' => $groupedVariations,
  //       'groupedOptions' => $groupedOptions,
  //     ];

  //     // Add this line to check the contents of $responseData
  //     dd($responseData);

  //     return response()->json($responseData, 200);
  //   } else {
  //     return response()->json('not found', 404);
  //   }
  // }



  public function get_related_products($category_id, $slug)
  {
    $relatedProducts = Product::with('category')->where('category_id', $category_id)->where('slug', '!=', $slug)->get();

    if ($relatedProducts) {
      return response()->json($relatedProducts, 200);
    } else {
      return response()->json('not found', 404);
    }
  }

  public function save_items(Request $request)
  {
    $user = $request->user(); // Get the authenticated user
    $cartItems = $request->input('cartItems');

    foreach ($cartItems as $cartItem) {

      $existingItem = Cart_item::where('cart_items.product_id', $cartItem['product_id'])
        ->first();

      if ($existingItem) {
        $existingItem->qty_cart += 1;
        $existingItem->save();
      } else {
        $cart = Cart::create(['user_id' => $user->id]);

        if ($cart) {
          $lastInsertedId = $cart->id;

          Cart_item::create([
            'cart_id' => $lastInsertedId,
            'product_id' => $cartItem['product_id'],
            'qty_cart' => 1,
          ]);
        }
      }
    }

    Activitylog::create([
      'user_id' => Auth::user()->id,
      'activity_type' => 'Save',
      'activity_description' => 'Create item',
      'user_agent' => $_SERVER['HTTP_USER_AGENT'],
      'ip_address' => $_SERVER['REMOTE_ADDR'],
    ]);

    return response()->json(['message' => 'Item added']);
  }

  public function load_ratings($prod_id)
  {

    $ratings = Rating::join('order_items', 'ratings.order_id', '=', 'order_items.order_id')
      ->join('orders', 'order_items.order_id', '=', 'orders.id')
      ->join('users', 'orders.user_id', '=', 'users.id')
      ->select('ratings.id as rating_id', 'ratings.rate_star', 'ratings.feedback', 'users.name', 'users.profile')
      ->where('order_items.product_id', $prod_id)->get();
    $totalRatings = $ratings->count();
    $averageRating = ($totalRatings > 0) ? $ratings->avg('rate_star') : 0;

    return response()->json([
      'ratings' => $ratings,
      'totalRatings' => $totalRatings,
      'averageRating' => $averageRating,
    ]);
  }

  public function show_feedback($prod_id, $star)
  {
    $get_feedbacks = null; // Initialize variable

    if ($star === 'All') {
      $get_feedbacks = Rating::join('order_items', 'ratings.order_id', '=', 'order_items.order_id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('users', 'orders.user_id', '=', 'users.id')
        ->select('ratings.id as rating_id', 'users.name', 'users.profile', 'ratings.feedback', 'ratings.rate_star')
        ->whereIn('rate_star', [5, 4, 3, 2, 1])
        ->where('order_items.product_id', $prod_id)->get();
    } else {
      $get_feedbacks = Rating::join('order_items', 'ratings.order_id', '=', 'order_items.order_id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('users', 'orders.user_id', '=', 'users.id')
        ->select('ratings.id as rating_id', 'users.name', 'users.profile', 'ratings.feedback', 'ratings.rate_star')
        ->where('order_items.product_id', $prod_id)->where('ratings.rate_star', $star)->get();
    }

    $customerRating = $get_feedbacks->avg('rate_star');

    return response()->json([
      'get_feedbacks' => $get_feedbacks,
      'customerRating' => $customerRating
    ]);
  }

  public function contact_us()
  {
    $company = Company_detail::select('address', 'phone', 'gmail', 'facebook', 'about')->find(1);
    return response()->json($company, 200);
  }

  public function categories()
  {
    $categories = Category::select('name', 'slug')->get();
    return response()->json($categories, 200);
  }


  public function load_slogan()
  {
    $slogan = Slogan::get();
    return response()->json($slogan, 200);
  }


  public function subscribe(Request $request)
  {
    $request->validate([
      'email' => 'required|email|unique:subscriptions,email',
    ]);

    // Create a new Subscription record
    Subscription::create([
      'email' => $request->input('email'),
    ]);

    return response()->json(['message' => 'Subscription successful!']);
  }
}
