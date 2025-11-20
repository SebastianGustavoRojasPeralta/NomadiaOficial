<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->only(['name','email','password']);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email','password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(['error'=>'invalid_credentials'], 401);
        }
        $user = Auth::user();
        // placeholder token (you should use Sanctum or Passport)
        $token = base64_encode($user->id . '|' . now());
        return response()->json(['token'=>$token]);
    }
}
