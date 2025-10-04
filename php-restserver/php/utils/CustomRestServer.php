<?php

namespace App\Utils;

use Jacwright\RestServer\RestServer as BaseRestServer;

class CustomRestServer extends BaseRestServer
{
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    public $capsule;
    
    /**
     * @var \stdClass
     */
    public $config;
    
    /**
     * @inheritDoc
     */
    public function __construct($mode = 'debug')
    {
        parent::__construct($mode);
    }
}
