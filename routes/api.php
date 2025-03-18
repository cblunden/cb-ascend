<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*
 * Uses Laravel Sanctum to create a token for the user. Pass in the "email" and "password"
 * in the request body to receive a token with the "borrow-create" and "borrow-complete" abilities.
 * Use the token in the Authorization header with the "Bearer" scheme to access the protected routes.
 */
Route::post('/tokens/create', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response('Could not authenticate', 503);
    }

    $token = $user->createToken('token-name', ['borrow-create', 'borrow-complete']);
    return ['success' => true, 'message' => 'Token created.', 'token' => $token->plainTextToken];
});

/*
 * The "borrow" and "return" routes are only accessible with the token provided by the "/tokens/create" route.
 */
Route::middleware('auth:sanctum')->namespace('App\Http\Controllers\Api')->group(function () {
    Route::post('/borrow/create', 'BookBorrowController@borrowBook')->middleware('ability:borrow-create')->name('borrow.create');
    Route::patch('/borrow/complete', 'BookBorrowController@returnBook')->middleware('ability:borrow-complete')->name('borrow.complete');
});
