<?php

//----------------------------------------------
//FILE NAME:  ProfileController.php gen for Servit Framework Controller
//Created by: Tlen<limweb@hotmail.com>
//DATE: 2022-12-04(Sun)  18:03:20 

//----------------------------------------------
use    Illuminate\Database\Capsule\Manager as Capsule;
use    \Jacwright\RestServer\RestException; 
use    Carbon\Carbon;

class ProfileController  extends JwtController {

    
    /**
    *@ noAuth
    *@url GET /me
    *----------------------------------------------
    *FILE NAME:  ProfileController.php gen for Servit Framework Controller
    *Created by: Tlen<limweb@hotmail.com>
    *DATE:  2022-12-04(Sun)  18:03:54 
    
    *----------------------------------------------
    */
    public function me(){
        try {
                $req = new \Request(); 
                $arrdata = $req->input->toArray(); 
                $jwtuser = $req->user; 
     
                $msg = 'สำเร็จ'; 
                $type='success'; //success,info,error,warning 
                $title='Successed!'; 
                $success = true; 
                 
                if(!$jwtuser) { 
                    throw new \Exception('401 Unauthorized',401);  
                    $success = false; 
                    $type ='error'; 
                    $title = 'Error!'; 
                    $msg = '401 Unauthorized';     
                } 
            
            return [
                //'ajax' => $ajax,
                //'page' => $page,
                //'perpage' => $perpage,
                //'skip' => $skip,
                //'total' => $total,
                //'count' => count($datas),
                //'datas' => $datas,
                //'columns' => $columns,
                //'info' => $info,
                //'infos' => $info,
                //'domains' => $domains,
                //'method' => $method,
                'status' => '1',
                'success' => $success,
                'msg' => $msg,
                'type' => $type,
                'title' => $title,
                'user' => $jwtuser,
                //'sql' => Capsule::getQueryLog(),
                'func'=> __CLASS__.'/'.__FUNCTION__
            ];
        } catch (\Exception $e) {
            //throw new \Jacwright\RestServer\RestException($e->getCode(),$e->getMessage());
            return[
                'status' => '0',
                'success'=> false,
                'msg'=> $e->getMessage(),
                'func'=> __CLASS__.'/'.__FUNCTION__,
            ]; 
        }
    }
    
    

}
