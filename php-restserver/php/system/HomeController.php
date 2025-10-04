<?php

//----------------------------------------------
//FILE NAME:  HomeController.php gen for Servit Framework Controller
//Created by: Tlen<limweb@hotmail.com>
//DATE: 2021-07-30(Fri)  23:17:25

//----------------------------------------------
use    Illuminate\Database\Capsule\Manager as Capsule;
use    \Jacwright\RestServer\RestException;
use    Carbon\Carbon;

class HomeController extends BaseController
{

    /**
     *@ noAuth
     *@url GET /
     *----------------------------------------------
     *FILE NAME:  HomeController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2021-07-30(Fri)  23:17:40

     *----------------------------------------------
     */
    public function index()
    {
        // echo "<center><h2Rest Server Api v0.0.1<h2></center>";
        include SRVPATH . '/dist/index.php';
    }



    /**
     *@noAuth
     *@url GET /test
     *----------------------------------------------
     *FILE NAME:  HomeController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2025-04-03(Thu)  02:41:34 
    
     *----------------------------------------------
     */
    public function test()
    {
        try {

            $req = new \Request();
            $arrdata = $req->input->toArray();
            $jwtuser = $req->user;
            $msg = 'สำเร็จ';
            $type = 'success'; //success,info,error,warning 
            $title = 'Successed!';
            $success = true;
            if ($req->statusCode != 200) {
                $this->server->setStatus($req->statusCode);
                throw new \Exception($req->errorMsg, $req->statusCode);
                $success = false;
                $type = 'error';
                $title = 'Error!';
                $msg = '401 Unauthorized';
            }
            if (!$jwtuser) {
                $this->server->setStatus(401);
                throw new \Exception('401 Unauthorized', 401);
                $success = false;
                $type = 'error';
                $title = 'Error!';
                $msg = '401 Unauthorized';
            }

            return [
                'status' => '1',
                'success' => $success,
                'msg' => $msg,
                'type' => $type,
                'title' => $title,
                //'sql' => Capsule::getQueryLog(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => '0',
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }
    }




    // /**
    //  *@noAuth
    //  *@url GET /invoices
    //  *----------------------------------------------
    //  *FILE NAME:  HomeController.php gen for Servit Framework Controller
    //  *Created by: Tlen<limweb@hotmail.com>
    //  *DATE:  2021-07-30(Fri)  23:17:40

    //  *----------------------------------------------
    //  */
    // public function invoices()
    // {
    //     $fdate = $_GET['FromDate'] ?: '';
    //     $tdate = $_GET['ToDate'] ?: '';
    //     // dump($fdate,$tdate);
    //     $rs = Capsule::select("Exec sp_Invoice_API ? , ? ",[$fdate,$tdate]);
    //     return $rs;
    //     // $rs = Menuuser::get();
    //     // return $rs;
    // }

    /**
     *@ noAuth
     *@url GET /home
     *----------------------------------------------
     *FILE NAME:  HomeController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2021-07-30(Fri)  23:17:40

     *----------------------------------------------
     */
    public function home()
    {
        include SRVPATH . '/dist/home.php';
    }


    /**
     *@noAuth
     *@url GET /404
     *----------------------------------------------
     *FILE NAME:  HomeController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2021-08-15(Sun)  18:37:08

     *----------------------------------------------
     */
    public function f404()
    {
        include SRVPATH . '/dist/404.html';
    }

    /**
     *@noAuth
     *@url GET /401
     *----------------------------------------------
     *FILE NAME:  HomeController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2021-08-15(Sun)  18:37:08

     *----------------------------------------------
     */
    public function f401()
    {
        include SRVPATH . '/dist/401.html';
    }
}
