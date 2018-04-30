<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;
use App\User;
use GuzzleHttp\Client as Http;

class RegisterController extends Controller
{
    public function __construct()
    {

    }
    public function register(Request $request)
    {
        $v = validator($request->only('name', 'email', 'password', 'phone_number', 'password_confirmation'), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone_number' => 'required'
        ]);
        if ($v->fails()) {
            return response()->json($v->errors()->all(), 400);
        } else {
            $data = request()->only('email', 'name', 'password');
            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => bcrypt('password'),
                'phone_number' => request('phone_number')
            ]);
            if ($user) {
                $client = Client::where('id', 2)->first();
                $http = new Http;

                $response = $http->post('http://localhost:8000/oauth/token', [
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_id' => '2',
                        'client_secret' => 'f6dHLqxt1Q9tL24PykEyj4YwzmKX9hZqNLLfaqSV',
                        'username' => $data['email'],
                        'password' => $data['password'],
                        'scope' => '*',
                    ],
                ]);

                return json_decode((string)$response->getBody(), true);
            } else {
                return response()->json([
                    'errorMessage' => 'Request could not be completed.'
                ], 400);
            }
        }
    }
}
