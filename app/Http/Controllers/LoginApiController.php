<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Token;
use Illuminate\Support\Str;
use DateTime;
class LoginAPIController extends Controller
{
    //
    public function  login(Request $req){

        $user = User::where('email',$req->email)->where('password',$req->password)->first();

        if($user){
            $api_token = Str::random(64);
            $token = new Token();
            $token->userid = $user->id;
            $token->token = $api_token;
            $token->created_at = new DateTime();
            $token->save();
            return $token;
        }
        return "No user found";

    }


} 