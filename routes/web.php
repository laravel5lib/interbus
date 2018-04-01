<?php

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

Horizon::auth(function ($request) {
    $env = App::environment();
    if (in_array($env, ['local', 'dev'])) {
        return true;
    }
    return false;
});

Route::get('/test', function (){
    $token = \App\Models\Token::first();
    dispatch(new \App\Jobs\Character\CharacterMiningJob($token))->onConnection('sync');
});

Route::view('/tokens/{code}', 'welcome')->name('ssocallbackcode');

Route::get('/tokens', function(\Illuminate\Http\Request $request){
    if ($request->has('code')){
        return redirect()->route('ssocallbackcode', ['code' => $request->get('code')]);
    }

    return view('welcome');
})->name('ssocallback');


Route::view('/{path?}', 'welcome')
    ->name('react')->where('path', '.*');

