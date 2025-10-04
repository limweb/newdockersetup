<?php

//----------------------------------------------
//FILE NAME:  BaseController.php gen for Servit Framework Controller
//Created by: Tlen<limweb@hotmail.com>
//DATE: 2022-05-19(Thu)  11:44:49

//----------------------------------------------
use    Illuminate\Database\Capsule\Manager as Capsule;
use    \Jacwright\RestServer\RestException;
use    Carbon\Carbon;

class BaseController
{
    /**
     * BaseController constructor.
     * This constructor will be called automatically when any child controller is instantiated.
     */
    /**
     * Initialization method that can be overridden by child controllers
     * This needs to be public to work with Jacwright RestServer
     */
    public function init()
    {
        $req = new \Request();
        $comp = $req->gets->comp;
        // $action = explode('?', $req->servers->REQUEST_URI)[0];
        if ($comp) {
            $this->company = $comp;
        } else {
            // dump('ไม่มี comp');
            $action = explode('/', $req->servers->REQUEST_URI)[0];
            if (strpos($action, '/api') === true) {
                include SRVPATH . '/dist/no_comp.html';
                exit;
            }
        }

        // $jwtuser = $req->user;
        // $this->member = $jwtuser;
    }
    /**
     * @var \Jacwright\RestServer\RestServer
     */
    public $server;

    /**
     * @var array
     */
    public $map = [];


    /**
     * @var object|null
     */
    public $member = null;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var string
     */
    public $format = 'json';

    /**
     * @var bool
     */
    public $useCors = true;


    public $company = null;
}
