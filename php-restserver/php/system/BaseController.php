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
}
