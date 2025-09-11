<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Auth\JWTAuth as JWTAuth;
class AuthController extends Controller
{
    use JWTAuth;

    public function __construct()
    {
        parent::__construct();
    }
public function login($request)
{
    $this->validate([
        'username||min:3|max:25',
        'mobile_number||length:11',
    ], $request);

    // Build query dynamically
    $query = $this->queryBuilder->table('users');

    if (isset($request->username)) {
        $query->where('username', '=', $request->username);
    }

    if (isset($request->mobile_number)) {
        $query->where('mobile_number', '=', $request->mobile_number);
    }

    $findUser = $query->get()->execute();

    // Check if user exists
    if (!$findUser) {
        return $this->sendResponse(
            message: "نام کاربری یا رمز عبور شما صحیح نیست!",
            error: true,
            status: HTTP_Unauthorized
        );
    }

    // Generate JWT token
    $token = $this->generateToken(
        $findUser->id,
        $findUser->username,
        $findUser->mobile_number,
        $findUser->role,
        $findUser->status
    );

    return $this->sendResponse(data: ['token' => $token], message: "با موفقیت وارد شدید");
}


    public function register($request){
        // validate request
        $this->validate([
            'username||required|min:3|max:25|string',
            'display_name||min:2|max:40|string',
            'mobile_number||required|length:11|string',
            'role||enum:admin,support,guest,host',
            'status||enum:pending,reject,accept',
        ], $request);
        $this->checkUnique(table: 'users', array: [['username', $request->username], ['mobile_number', $request->mobile_number]]);

        $newUser = $this->queryBuilder->table('users')        
            ->insert([
                'username' => $request->username,
                'display_name' => $request->display_name ?? NULL,
                'mobile_number' => $request->mobile_number,
                'profile_image' => $request->profile_image ?? NULL,
                'role' => $request->role ?? 'guest',
                'status' => $request->status ?? 'pending',
                'created_at' => time(),
                'updated_at' => time(),
            ])->execute();

        return $this->sendResponse(data: $newUser, message: "حساب کاربری شما با موفقیت ایجاد شد!");
    }

    public function verify($request){
        $verification = $this->verifyToken($request->token);

        return $this->sendResponse(data:$verification, message: "Unauthorized token body!" ,error: true, status: HTTP_BadREQUEST);
    }
}