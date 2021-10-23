<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ConfigController;
use App\Http\Controllers\API\ContentController;
use App\Http\Controllers\API\FrontEndController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProducerController;
use App\Http\Controllers\API\UserStaffController;
use App\Http\Controllers\ProductController;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::get('footer',[FrontEndController::class,'footer']);

Route::get('home',[HomeController::class, 'index']);

//product
Route::get('product',[FrontEndController::class, 'viewproduct']);
Route::get('fetchproducts/{slug}',[FrontEndController::class, 'viewproductcategory']);
Route::get('viewproductdetail/{category_slug}/{product_id}', [FrontEndController::class, 'productdetail']);

Route::get('detailcontent/{content}', [FrontEndController::class, 'detailcontent']);

//cart
Route::post('add-to-cart',[CartController::class, 'store']);
Route::get('cart', [CartController::class, 'viewcart']);
Route::put('update-cart/{cartID}/{scope}', [CartController::class, 'updatecart']);
Route::delete('deleteitemcart/{cartID}', [CartController::class, 'deleteitemcart']);
//checkout
Route::get('getUser', [OrderController::class, 'getUSer']);
Route::post('select-district/{provinceid}', [OrderController::class, 'selectdistrict']);
Route::post('place-order', [OrderController::class, 'placeorder']);
Route::post('validate-order', [OrderController::class, 'validateorder']);


Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function () {

    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => "You are in", 'status' => 200], 200);
    });
    //category
    Route::post('store-category', [CategoryController::class, 'store']);
    Route::get('view-category', [CategoryController::class, 'index']);
    Route::get('edit-category/{id}', [CategoryController::class, 'edit']);
    Route::post('update-category/{id}', [CategoryController::class, 'update']);
    Route::delete('delete-category/{id}', [CategoryController::class, 'destroy']);
    Route::get('all-category', [CategoryController::class, 'allcategory']);


    //Producer
    Route::post('store-producer', [ProducerController::class, 'store']);
    Route::get('view-producer', [ProducerController::class, 'index']);
    Route::get('edit-producer/{id}', [ProducerController::class, 'edit']);
    Route::post('update-producer/{id}', [ProducerController::class, 'update']);
    Route::delete('delete-producer/{id}', [ProducerController::class, 'destroy']);
    Route::get('all-producer', [ProducerController::class, 'allproducer']);

    //Product
    Route::post('store-product', [ProductController::class, 'store']);
    Route::get('view-product', [ProductController::class, 'index']);
    Route::get('edit-product/{id}', [ProductController::class, 'edit']);
    Route::post('update-product/{id}', [ProductController::class, 'update']);
    Route::delete('delete-product/{id}', [ProductController::class, 'destroy']);

    //content
    Route::post('add-news', [ContentController::class, 'store']);
    Route::get('view-news', [ContentController::class, 'index']);
    Route::get('edit-news/{id}', [ContentController::class, 'edit']);
    Route::post('update-news/{id}', [ContentController::class, 'update']);
    Route::delete('delete-news/{id}', [ContentController::class, 'destroy']);

    //user-staff
    //view user
    Route::get('view-users', [UserStaffController::class, 'viewusers']);
    Route::get('view-staff', [UserStaffController::class, 'viewstaff']);
    //become
    Route::put('isAdmin/{id}', [UserStaffController::class, 'becomeAdmin']);
    Route::put('isUser/{id}', [UserStaffController::class, 'becomeUser']);

    //config
    Route::get('edit-config/{id}', [ConfigController::class, 'edit']);
    Route::put('update-config/{id}', [ConfigController::class, 'update']);

  
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
});
