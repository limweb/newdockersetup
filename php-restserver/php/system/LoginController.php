<?php

use    Illuminate\Database\Capsule\Manager as Capsule;
use    Carbon\Carbon;

class LoginController  extends JwtController
{


    /**
     *@noAuth
     *@url GET /logout
     *----------------------------------------------
     *FILE NAME:  LoginController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2022-10-08(Sat)  16:16:49

     *----------------------------------------------
     */
    public function logout()
    {
        echo '<script>
                document.cookie = "user=";
                document.cookie = "ref_token=";
                document.cookie = "pubkey=";
                location.href = "/login"
            </script>';
    }



    /**
     *@noAuth
     *@url GET /login
     *----------------------------------------------
     *FILE NAME:  LoginController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2022-10-08(Sat)  15:49:46

     *----------------------------------------------
     */
    public function loginfrm()
    {
        $html = '
        <script src="https://cdn.jsdelivr.net/npm/@unocss/runtime"></script>
<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 flex flex-col">
    <div class="mb-4">
      <label class="block text-grey-darker text-sm font-bold mb-2" for="username">
        Username
      </label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" id="username" type="text" placeholder="Username">
    </div>
    <div class="mb-6">
      <label class="block text-grey-darker text-sm font-bold mb-2" for="password">
        Password
      </label>
      <input class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3" id="password" type="password" placeholder="******************">
    </div>
    <div class="flex items-center justify-between">
      <button  onclick="login()" class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded" type="button">
        Sign In
      </button>
    </div>
</div>
<script>
        function login() {
            let username = document.querySelector("#username").value;
            let password = document.querySelector("#password").value;
            // console.log("-----login-----",username,password);
            fetch("/login",{
                headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
                },
                method: "POST",
                body: JSON.stringify({ username, password })
            }).then((response) => response.json()).then((data) => {
                console.log(data)
                document.cookie = "user="+JSON.stringify(data.user);
                document.cookie = "ref_token="+data.token;
                document.cookie = "pubkey="+data.pubkey;
                if(data.success){
                    location.href = "/routes"
                }

            });
        }

</script>
';
        echo $html;
    }



    /**
     *@noAuth
     *@url POST /login
     *----------------------------------------------
     *FILE NAME:  LoginController.php gen for Servit Framework Controller
     *----------------------------------------------
     */
    public function login()
    {
        try {
            $arrdata = \Request::getInstance()->input->toArray();
            $user = LoginService::login($arrdata);
            // dump($arrdata,$user);
            if ($user) {
                $jwt = JwtService::getToken($user);
                return [
                    "user" => $user,
                    "token" => $jwt["token"],
                    "pubkey" => $jwt["pubkey"],
                    "status" => "1",
                    "success" => true,
                    "key" => USERJWT,
                ];
            } else {
                // throw new Exception("กรุณา login ใหม่!", 1);
                $this->server->setStatus(401);
                throw new \Exception('401 Unauthorized', 401);
            }
        } catch (Exception $e) {
            return [
                "status" => "0",
                "success" => false,
                "msg" => $e->getMessage(),
            ];
        }
    }
}
