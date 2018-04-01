<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

Route::get('/tokens', function (Request $request) {

    if (!Auth::user()) {
        abort(403);
    }

    $perPage = $request->get('itemsPerPage') ?? 20;
    $offset = $request->get('offset') ?? 0;

    $user = Auth::user();
    $tokens = $user->tokens()->limit($perPage)->offset($offset)->orderBy('character_name', 'asc')->get();
    $tokens->pull('access_token');
    $count = $user->tokens()->count();
    return [
        'data' => $tokens,
        'count' => $count
    ];
});

Route::post('/tokens/{token}', function ($code, \tristanpollard\ESIClient\Services\SSO $sso) {

    $user = Auth::user();

    if (!$user) {
        abort(403);
    }

    $token = collect($sso->processCode($code, $user));
    $scopes = $token->pull('scopes');

    \DB::transaction(function () use ($scopes, $token, $user) {
        $existingToken = \App\Models\Token::where('character_id', $token->get('character_id'))->first();
        if ($existingToken) {
            $existingToken->scopes()->delete();
            $existingToken->delete();
        }
        $tokenModel = new \App\Models\Token($token->toArray());
        $user->tokens()->save($tokenModel);
        foreach ($scopes as $scope) {
            $scopeModel = new \App\Models\Scope();
            $scopeModel->scope = $scope;
            $tokenModel->scopes()->save($scopeModel);
        }

        if (!$existingToken) {
            dispatch(new \App\Jobs\AuthenticatedCharacterUpdateQueuer($tokenModel));
        }
    });


    return $token;
});

Route::get('/ssourl', function (\tristanpollard\ESIClient\Services\SSO $sso) {
    return $sso->generateSSOUrl();
});

Route::get('/characters/{character}/mail', 'Api\CharacterController@getcharacterMail');
Route::get('/characters/{character}/roles', 'Api\CharacterController@getCharacterRoles');
Route::get('/characters/{character}/titles', 'Api\CharacterController@getCharacterTitles');
Route::get('/characters/{character}/online', 'Api\CharacterController@getCharacterOnline');
Route::get('/characters/{character}/skills', 'Api\CharacterController@getCharacterSkills');
Route::get('/characters/{id}/fatigue', 'Api\CharacterController@getCharacterFatigue');
Route::get('/characters/{id}/journal', 'Api\CharacterController@getCharacterJournal');
Route::get('/characters/{id}/contacts', 'Api\CharacterController@getCharacterContacts');
Route::get('/characters/{id}', 'Api\CharacterController@getCharacter');
Route::get('/characters', 'Api\CharacterController@getCharacters');

Route::get('/stats', function () {
    return [
        [
            'stat' => 'Members',
            'value' => \App\User::count()
        ],
        [
            'stat' => 'Tokens',
            'value' => \App\Models\Token::count()
        ]
    ];
});

Route::get('/{any?}', function ($any = null) {
    return ['route' => $any];
});