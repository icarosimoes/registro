<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::prefix('solter')->group(function(){
    //Route::post('cost_center/checkCode', 'Register\CostCenterController@checkCode');
   });

   Route::get('solter/live', function(){
       return "aqui aqui";
   });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


