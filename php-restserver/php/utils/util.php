<?php

function includeDir($path)
{
    $dir = new \RecursiveDirectoryIterator($path);
    $iterator = new \RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        $fname = $file->getFilename();
        if (preg_match('%\.php$%', $fname)) {
            if ($fname != 'index.php') require_once $file->getPathname();
        }
    }
}

function includeDirClass($path, $basePath = '', $server = null, $usedname = false, $namespaceinclass = false)
{

    //1. usedname เอา name ก่อน Controler มาใช้ในการ route
    //2. namespaceinclass มีหรือไม่มี namespace ใน  Controler ถ้ามีจะต้อง require include
    //3. basepath  ก็คือ basepath ในการ route

    $dir = new \RecursiveDirectoryIterator($path);
    $iterator = new \RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        $fname = $file->getFilename();
        if (preg_match('%\.php$%', $fname)) {
            $namespace = "";
            if ($basePath) {
                $basePathx = $basePath;
            } else {
                $basePathx = '/' . $iterator->getSubPath();
                if ($namespaceinclass) {
                    $namespace = $iterator->getSubPath();
                }
            }
            // dump('file fullpath--->' . $file->getPathname(), 'basepath--->' . $basePathx, 'namespace-->' . $namespace);
            if ($fname != 'index.php') {
                require_once $file->getPathname();
                if (preg_match('/.*Controller\.php/', $fname)) {   // if = *Controller.php
                    $basePathx = str_replace(['\\'], '/', $basePathx);
                    if ($namespaceinclass) {
                        $namespace = str_replace(['/'], '\\', $basePathx);
                    }
                    $className = basename($fname, '.php');
                    if ($usedname) {
                        $re = '/(.*)Controller/m';
                        preg_match_all($re, $className, $matches, PREG_SET_ORDER, 0);
                        // dump($className, $basePathx, $namespace);
                        if ($basePathx) {
                            $basePathx .= '/' . strtolower($matches[0][1]);
                        } else {
                            $basePathx .=  strtolower($matches[0][1]);
                        }
                    }
                    if ($namespace == '\\') $namespace = '';
                    if ($namespace != '') {
                        $clx = $namespace . '\\' . $className;
                    } else {
                        $clx = $className;
                    }
                    // dump(
                    //     'file fullpath--->' . $file->getPathname(),
                    //     'basepath-->' . $basePathx,
                    //     'namespace-->' . $namespace,
                    //     'classname-->' . $clx
                    // );
                    if ($server) $server->addClass($clx, $basePathx);
                }
            }
        }
    }
}

function replaceslag($str)
{
    return  str_replace(['//'], '/', $str);
}

function replacebslag($str)
{
    return  str_replace(['\\\\'], '\\', $str);
}

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}


if (!function_exists('implodeKV')) {
    function implodeKV($glueKV, $gluePair, $KVarray)
    {
        if (is_object($KVarray)) {
            $KVarray = json_decode(json_encode($KVarray), true);
        }
        $t = array();
        foreach ($KVarray as $key => $val) {
            if (is_array($val)) {
                $val = implodeKV(':', ',', $val);
            } elseif (is_object($val)) {
                $val = json_decode(json_encode($val), true);
                $val = implodeKV(':', ',', $val);
            }

            if (is_int($key)) {
                $t[] = $val;
            } else {
                $t[] = $key . $glueKV . $val;
            }
        }
        return implode($gluePair, $t);
    }
}

if (!function_exists('consolelog')) {
    function consolelog($status = 200)
    {
        $lists = func_get_args();
        $status = '';
        $status = implodeKV(':', ' ', $lists);
        if (isset($_SERVER["REMOTE_ADDR"]) && !empty($_SERVER["REMOTE_ADDR"])) {
            $raddr = $_SERVER["REMOTE_ADDR"];
        } else {
            $raddr = '127.0.0.1';
        }
        if (isset($_SERVER["REMOTE_PORT"]) && !empty($_SERVER["REMOTE_PORT"])) {
            $rport = $_SERVER["REMOTE_PORT"];
        } else {
            $rport = '8000';
        }

        if (isset($_SERVER["REQUEST_URI"]) && !empty($_SERVER["REQUEST_URI"])) {
            $ruri = $_SERVER["REQUEST_URI"];
        } else {
            $ruri = '/console';
        }
        file_put_contents(
            "php://stdout",
            sprintf(
                "[%s] %s:%s [%s]:%s \n",
                date("D M j H:i:s Y"),
                $raddr,
                $rport,
                $status,
                $ruri
            )
        );
    }
} // end-of-check funtion exist

if (!function_exists('logAccess')) {
    function logAccess($status = 200)
    {
        file_put_contents("php://stdout", sprintf(
            "[%s] %s:%s [%s]: %s\n",
            date("D M j H:i:s Y"),
            $_SERVER["REMOTE_ADDR"],
            $_SERVER["REMOTE_PORT"],
            $status,
            $_SERVER["REQUEST_URI"]
        ));
    }
}

if (!function_exists('saveImg')) {
    //---- save image ----------
    /**
     * @param base64 string $b64img
     * @param string|null $imgname
     * @param string $filepath
     * @return string
     **/
    function saveImg($b64img, $imgname = null, $filepath = "/images/")
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        try {
            if ($b64img) {
                if (empty($imgname)) {
                    $output_file = uniqid() . '.png';
                } else {
                    $output_file = $imgname;
                }
                file_put_contents(SRVPATH . $filepath . $output_file, file_get_contents($b64img));
                chmod(SRVPATH . $filepath . $output_file, 0664);
                return $output_file;
            } else {
                if ($imgname) return $imgname;
                return  '';
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
//------------- INIT----------------------------------------
