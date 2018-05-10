<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Laravel\Passport\ApiTokenCookieFactory;
use Laravel\Passport\Passport;

class AuthController extends Controller
{

    public function user(Request $request) {
        return Auth::user();
    }

    public function login(Request $request, ApiTokenCookieFactory $cookieFactory)
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar;
        $client = new \GuzzleHttp\Client([
            'base_uri' => config('app.url'),
            'cookies' => $jar
        ]);
        $req = $client->post('/oauth/token',
            [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('PASSPORT_PASSWORD_ID'),
                    'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
                    'username' => $request->get('username'),
                    'password' => $request->get('password'),
                ],
                'headers' => [
                    'Accept' => 'application/json'
                ],
            ]
        );
        $token = json_decode($req->getBody(), true);
        $response = Response()->json($token);
        if ( isset($token['access_token']) ) {
            $user = $client->get('/api/auth/user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token['access_token']
                ]
            ]);
            $user = json_decode($user->getBody(), true);
            if ( isset($user['id']) ) {
                Auth::guard('web')->login(User::find($user['id']));
            }
        }
        return $response;
    }

}
