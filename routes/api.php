<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OAIController;
use App\Http\Controllers\ItemController;
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

// routes/api.php
Route::get('/oai', [OAIController::class, 'handleRequest'])->name('oai.pmh');

// routes/api.php
Route::apiResource('items', ItemController::class);
Route::get('collections/{collection}/items', [ItemController::class, 'byCollection']);
Route::post('items/{item}/download', [ItemController::class, 'download']);

Route::apiResource('/collections', 'Api\CollectionController');
Route::get('/search', 'Api\SearchController@index');
Route::post('/submit', 'Api\SubmitController@store'); // SWORD-like

// routes/api.php
Route::get('/items', function() {
    return \App\Models\Item::with('collection.community')
        ->where('status', 'published')
        ->get()
        ->map(function($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'metadata' => $item->metadata,
                'collection' => $item->collection->name,
            ];
        });
});// routes/api.php
Route::get('/items', function() {
    return \App\Models\Item::with('collection.community')
        ->where('status', 'published')
        ->get()
        ->map(function($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'metadata' => $item->metadata,
                'collection' => $item->collection->name,
            ];
        });
});
