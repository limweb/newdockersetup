<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// Use the custom RestServer class from utils namespace
use App\Utils\RestServer;

define('USERCLASS', $_ENV['USERCLASS'] ?: 'Login');
$userfield = [
    'exp'   => 1,  // 8 hours
    'iss'   => 'name_thai',
    'iat'   => 'iat',
    'sub'   => 'user_name',
    'aud'   => 'user_name',
    'name'  => ['user_name'],
    'level' => 'user_level',
    'uid'   => 'user_name',
];
define('USERJWT', $userfield);

$capsule = new Capsule;
$dbconfig = [
    'driver'   => $_ENV['DB_CONNECTION'],
    'host'     => $_ENV['DB_HOST'],
    'port'     => $_ENV['DB_PORT'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
    'charset'  => $_ENV['DB_CHARSET'],
    'prefix'   => $_ENV['DB_PREFIX'],
    // 'driver'   => 'sqlsrv',
    // 'host'     => '127.0.0.1',
    // 'port'     => '1433',
    // 'database' => 'test',
    // 'username' => 'sa',
    // 'password' => 'mssql2017@pass',
    // 'charset'  => 'utf8',
    // 'prefix'   => '',
];

// $dbconfig1 = [
//     'driver'   => $_ENV['DB1_CONNECTION'] ?: 'mysql',
//     'host'     => $_ENV['DB1_HOST'] ?: '127.0.0.1',
//     'port'     => $_ENV['DB1_PORT'] ?: '3306',
//     'database' => $_ENV['DB1_NAME'] ?: 'dbname',
//     'username' => $_ENV['DB1_USER'] ?: 'dbuser',
//     'password' => $_ENV['DB1_PASS'] ?: '',
//     'charset'  => 'utf8',
//     'prefix'   => '',
// ];

// $dbconfig2 = [
//     'driver'   => $_ENV['DB2_CONNECTION'] ?: 'mysql',
//     'host'     => $_ENV['DB2_HOST'] ?: '127.0.0.1',
//     'port'     => $_ENV['DB2_PORT'] ?: '3306',
//     'database' => $_ENV['DB2_NAME'] ?: 'dbname',
//     'username' => $_ENV['DB2_USER'] ?: 'dbuser',
//     'password' => $_ENV['DB2_PASS'] ?: '',
//     'charset'  => 'utf8',
//     'prefix'   => '',
// ];

// $dbconfig3 = [
//     'driver'   => env('DB3_CONNECTION','sqlsrv'),
//     'host'     => env('DB3_HOST','127.0.0.1'),
//     'port'     => env('DB3_PORT','1433'),
//     'database' => env('DB3_DATABASE','db_ERP'),
//     'user'     => env('DB3_USERNAME','db_admin'),
//     'password' => env('DB3_PASSWORD','123456789'),
//     'charset'  => env('DB3_CHARSET',''),
//     'prefix'   => env('DB3_PREFIX',''),
// ];


// $dbconfig4 = [
//     'driver'   => $_ENV['DB4_CONNECTION'] ?: 'mysql',
//     'host'     => $_ENV['DB4_HOST'] ?: '127.0.0.1',
//     'port'     => $_ENV['DB4_PORT'] ?: '3306',
//     'database' => $_ENV['DB4_NAME'] ?: 'dbname',
//     'username' => $_ENV['DB4_USER'] ?: 'dbuser',
//     'password' => $_ENV['DB4_PASS'] ?: '',
//     'charset'  => 'utf8',
//     'prefix'   => '',
// ];

$capsule->addConnection($dbconfig);
// $capsule->addConnection($dbconfig2,'membersystem');
// $capsule->addConnection($dbconfig3);
// $capsule->addConnection($dbconfig4);

// use Illuminate\Events\Dispatcher;
// use Illuminate\Container\Container;
// $capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();
$connection = $capsule->getConnection('default');
$config = new \Config();
$config->dbconfig = $dbconfig;
// $config->dbconfig1 = $dbconfig1;
// $config->dbconfig2 = $dbconfig2;
// $config->dbconfig3 = $dbconfig3;
// Create new instance of our custom RestServer
$server = new RestServer();
$server->setFormat('application/json');
$server->capsule = $capsule;
$server->config = $config;
