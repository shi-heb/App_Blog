<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use Dotenv\Exception\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;
use App\Repositories\CustomResponse;
use Illuminate\Auth\Events\Registered;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
           "email.required" => "vous devez specifier votre mail",
           "password.required" => "vous devez specifier votre password",
           "name.required" => "vous devez specifier votre nom",
           

         
        ];
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ],$messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
   

    public function register(Request $request){
        $email = request('email') ?: null;
        //UserRepository::alreadySigned($email);

        $validator = null;
        try {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                throw new ValidationException();
            }
        } catch (ValidationException $e) {
            return response()->json($validator->errors(), 422);
        }
        event(new Registered($user = $this->create($request->all())));
        if ($response = $this->registered($request, $user)) {
            return $response;
        }


        

    }


    protected function registered(Request $request, $user)
    {
        if ($user) {
            $token = JWTAuth::fromUser($user);
            return CustomResponse::respondWithToken($token, $user);
        }
        return response()->json([
            'status' => 'fail',
            'user' => null
        ], 400);
    }


    protected function create(array $data)
    {
        $user = UserRepository::create( $data['name'],$data['email'], $data['password']);

       
        return $user;
    }







}
