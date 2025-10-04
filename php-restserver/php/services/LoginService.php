<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class LoginService  extends BaseService
{
    public static function login($arrdata)
    {
        $user = Login::where("user_name", $arrdata["username"])->where("a_password", $arrdata["password"])->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }
}