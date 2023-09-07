<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|admin|other-segments).*')->name('app');
//  we're using the where method to specify a regular expression constraint on the any parameter in the URL, which excludes api, admin, or any other specific segments that you don't want to match. This helps to avoid conflicts with other routes that may have more specific matching patterns. Additionally, we're giving the route a name (app) for easier reference in other parts of your application. Note: Please adjust the regular expression ^(?!api|admin|other-segments).* to match your specific requirements, and make sure to place this catch-all route at the end of your routes file after all other routes.


// Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();
