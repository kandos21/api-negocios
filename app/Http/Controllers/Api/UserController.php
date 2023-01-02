<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();
        return response()->json([
            "status" => 1,
            "msg" => 'alta de usuario exitosa'
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([

            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where("email", "=", $request->email)->first();
        if (isset($user->id)) {
            if (hash::check($request->password, $user->password)) {
                # creamos el token
                $token=$user->createToken("auth_token")->plainTextToken;
                return response()->json([
                    "status" => 1,
                    "msg" => 'usuario logueado exitosamente',
                    "access_token"=>$token
                ]); 
            }
            else{
                return response()->json([
                    "status" => 0,
                    "msg" => 'la password es incorrecta'
                ],404);  
            }
        }
        else{
            return response()->json([
                "status" => 0,
                "msg" => 'usario no registrado'
            ],404);  

        }
    }
    public function userProfile ()
    {
        return response()->json([
            "status" => 0,
            "msg" => 'acerca del perfil del usuario',
            "data"=>auth()->user()
        ]);  
    }
    public function logout()
    {   
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" => 0,
            "msg" => 'cierre de sesion',
            
        ]); 
    }
}
