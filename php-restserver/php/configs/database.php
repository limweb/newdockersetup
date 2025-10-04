<?php
require_once __DIR__.'/../vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

$capsule = new Capsule;

$dbconfig = [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'db_oauth',
    'username' => 'dbuser',
    'password' => 'dbpass',
    'charset'  => 'utf8',
    'prefix'   => '',
];
$capsule->addConnection($dbconfig);
// use Illuminate\Events\Dispatcher;
// use Illuminate\Container\Container;
// $capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->bootEloquent();
$capsule->setAsGlobal();

