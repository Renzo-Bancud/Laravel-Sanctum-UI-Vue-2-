<?php

use App\Http\Controllers\CategoryController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('user', [App\Http\Controllers\AuthController::class, 'update_user']);
    Route::post('verify', [App\Http\Controllers\AuthController::class, 'verifyEmail']);
    Route::post('request-code', [App\Http\Controllers\AuthController::class, 'requestAnotherCode']);
    Route::post('get-new-code', [App\Http\Controllers\AuthController::class, 'getNewCode']);
    Route::post('renew-password', [App\Http\Controllers\AuthController::class, 'renew_password']);
  
   

    //Admin Side

    //Dashboard
    Route::get('load-dashboard-data', [App\Http\Controllers\HomeController::class, 'load_dashboard_data']);
    Route::get('complete-order-chart', [App\Http\Controllers\HomeController::class, 'complete_order_chart']);

    //Header
    Route::get('load-count-ratings', [App\Http\Controllers\HomeController::class, 'load_count_ratings']);
    Route::get('get-all-ratings', [App\Http\Controllers\HomeController::class, 'get_all_ratings']);

    //Sidebar
    Route::resource('product', 'App\Http\Controllers\ProductsController');
    Route::resource('category', 'App\Http\Controllers\CategoryController');
    Route::resource('product_delivery', 'App\Http\Controllers\DeliveriesController');
    Route::resource('customer', 'App\Http\Controllers\CustomersController');
    Route::resource('slogan', 'App\Http\Controllers\SloganController');
    Route::resource('variation', 'App\Http\Controllers\VariationController');
    Route::get('variation-selection', [App\Http\Controllers\VariationController::class, 'variation_selection']);
    Route::get('category-selection', [App\Http\Controllers\CategoryController::class, 'category_selection']);
    Route::get('track-orders', [App\Http\Controllers\OrdersController::class, 'track_orders']);
    Route::post('mark-order', [App\Http\Controllers\OrdersController::class, 'mark_order']);
    Route::post('cancel-order', [App\Http\Controllers\OrdersController::class, 'cancel_order']);
    Route::get('product-selection', [App\Http\Controllers\DeliveriesController::class, 'product_selection']);
    Route::get('product-inventory', [App\Http\Controllers\DeliveriesController::class, 'product_inventory']);
    Route::get('activity-logs', [App\Http\Controllers\HomeController::class, 'activity_logs']);
    Route::get('company-details', [App\Http\Controllers\HomeController::class, 'company_details']);
    Route::post('update-company-details', [App\Http\Controllers\HomeController::class, 'update_company_details']);



    //*CLIENT SIDE

    //my cart component
    Route::post('my-cart', [App\Http\Controllers\CartController::class, 'my_cart']);
    Route::get('load-my-cart', [App\Http\Controllers\CartController::class, 'load_my_cart']);
    Route::post('remove-order-item/{cart_id}', [App\Http\Controllers\CartController::class, 'remove_item']);
    Route::post('remove-pending-order-item/{order_id}', [App\Http\Controllers\CartController::class, 'remove_pending_item']);
    Route::post('proceed-to-checkout', [App\Http\Controllers\CartController::class, 'proceed_to_checkout']);
    Route::get('track-order-status', [App\Http\Controllers\CartController::class, 'track_order_status']);

    //to checkout component
    Route::get('load-ordered-item', [App\Http\Controllers\CartController::class, 'load_ordered_item']);
    Route::post('delivery-information', [App\Http\Controllers\CheckoutController::class, 'delivery_information']);
    Route::post('edit-delivery-information/{contact_id}', [App\Http\Controllers\CheckoutController::class, 'edit_delivery_information']);
    Route::get('get-billing-address/{uid}', [App\Http\Controllers\CheckoutController::class, 'billing_address']);
    Route::post('remove-selected-item/{cart_item_id}', [App\Http\Controllers\CartController::class, 'remove_selected_item']);

    //Orders
    Route::post('update-payment-type', [App\Http\Controllers\CartController::class, 'update_payment_type']);
    Route::get('placed-order/{uid}', [App\Http\Controllers\CartController::class, 'placed_order']);
    Route::get('placed-order-details/{uid}', [App\Http\Controllers\CartController::class, 'placed_order_details']);
    Route::get('get-order-items-status/{uid}', [App\Http\Controllers\CartController::class, 'get_items_status']);
    Route::get('get-order-items-status-details/{uid}', [App\Http\Controllers\CartController::class, 'get_items_status_details']);
    Route::post('cancel-item', [App\Http\Controllers\CartController::class, 'cancel_item']);

    //Purchases
    Route::get('get-purchase-orders/{uid}', [App\Http\Controllers\CartController::class, 'purchase_orders']);
    Route::post('save-rating', [App\Http\Controllers\CartController::class, 'save_rating']);

    //Account
    Route::post('edit-account-information/{user_id}', [App\Http\Controllers\AuthController::class, 'edit_account_information']);
    Route::get('load-user', [App\Http\Controllers\AuthController::class, 'load_user']);

    //*END OF CLIENT SIDE

    //*ENCRYPTION TO VUE
    Route::post('/encrypt', function (Illuminate\Http\Request $request) {
        $data = $request->input('data');
        $encryptedData = Crypt::encrypt($data);

        return response()->json(['encryptedData' => $encryptedData]);
    });



});

//PUBLIC API
Route::get('products', [App\Http\Controllers\PublicAPIController::class, 'products']);
Route::get('sale-products', [App\Http\Controllers\PublicAPIController::class, 'sale_products']);
Route::get('load-category-products', [App\Http\Controllers\PublicAPIController::class, 'load_category_products']);
Route::get('product-categories', [App\Http\Controllers\PublicAPIController::class, 'product_categories']);
Route::get('product/item/{slug}', [App\Http\Controllers\PublicAPIController::class, 'product_details']);
Route::get('product/get-related-product/{category_id}/{slug}', [App\Http\Controllers\PublicAPIController::class, 'get_related_products']);
Route::post('save-items', [App\Http\Controllers\PublicAPIController::class, 'save_items']);
Route::get('load-ratings/{prod_id}', [App\Http\Controllers\PublicAPIController::class, 'load_ratings']);
Route::get('show-feedback/{prod_id}/{star}', [App\Http\Controllers\PublicAPIController::class, 'show_feedback']);
Route::get('contact-us', [App\Http\Controllers\PublicAPIController::class, 'contact_us']);
Route::get('categories', [App\Http\Controllers\PublicAPIController::class, 'categories']);
Route::get('load-slogan', [App\Http\Controllers\PublicAPIController::class, 'load_slogan']);
Route::post('forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [App\Http\Controllers\AuthController::class, 'reset']);
Route::post('delete-unverified', [App\Http\Controllers\AuthController::class, 'delete_unverified']);
Route::post('subscribe', [App\Http\Controllers\PublicAPIController::class, 'subscribe']);
