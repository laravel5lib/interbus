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
    //$token = \App\Models\Token::where('character_id', 94320234)->first();
    $token = \App\Models\Token::first();
    $char = $token->character_id;
    $time = \Carbon\Carbon::now();
    dispatch(new \App\Jobs\Character\CharacterWalletJournalJob($token))->onConnection('sync');
    return \Carbon\Carbon::now()->diffInSeconds($time);
    //return \App\Models\Character\CharacterJournalEntry::whereNotNull('first_party_id')->doesntHave('firstParty')->get();
    //dispatch(new \App\Jobs\Character\CharacterClonesJob($token))->onConnection('sync');
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
