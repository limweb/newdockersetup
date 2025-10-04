<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/utils/util.php';
require_once __DIR__ . '/utils/RestServer.php';
require_once __DIR__ . '/utils/Request.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dotenv = \Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->load();

// header("Access-Control-Allow-Origin: *");
if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else {
    date_default_timezone_set("Asia/Bangkok");
    define('SRVPATH', __DIR__);
    $server = new \App\Utils\RestServer();
    require_once __DIR__ . '/configs/config.php';
    $server->useCors = true;
    require_once __DIR__ . '/system/BaseModel.php';
    require_once __DIR__ . '/system/SecureController.php';
    require_once __DIR__ . '/system/JwtController.php';
    includeDir(__DIR__ . '/libs');
    includeDir(__DIR__ . '/system');
    // includeDirClass(__DIR__ . '/system', $basePath = '', $server);
    includeDirClass(__DIR__ . '/system', $basePath = '', $server, false, true);
    // require_once __DIR__ . '/configs/pdoconfig.php';
    includeDir(__DIR__ . '/models');
    includeDir(__DIR__ . '/services');
    // require_once __DIR__.'/models/Menuuser.php';
    // includeDirClass(__DIR__ . '/controllers', $basePath = '/api', $server, true);
    // includeDirClass(__DIR__ . '/controllers', $basePath = '/api', $server, $usedname = true, $namespaceinclass = false);
    includeDirClass(__DIR__ . '/controllers', $basePath = 'api', $server, $usedname = false, $namespaceinclass = false);
    $server->allowedOrigin = '*';
    // $server->allowedOrigin = ['http://admin.warroomcovid.com','https://admin.warroomcovid.com','http://www.warroomcovid.com','https://www.warroomcovid.com','http://api.warroomcovid.com','https://api.warroomcovid.com','http://warroomcovid.com','https://warroomcovid.com',];
    new Request();
    $server->handle();
}
