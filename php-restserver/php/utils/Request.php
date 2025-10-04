<?php
include_once __DIR__.'/Config.php';
include_once __DIR__.'/SingletonTrait.php';
class Request extends Config
{

    use  SingletonTrait;
    
    public function __construct($jsonAssoc = false)
    {
        parent::__construct();
        $this->jsonAssoc = $jsonAssoc;
        $this->input();
        $this->inputget();
        $this->inputpost();
        $this->inputfiles();
        $this->cookies();
        $this->sessions();
        $this->servers();
        $this->header();
        $this->setAsGlobal();
        $this->verify = false;
        $this->errorMsg = "";
        $this->statusCode = 200;

        if(empty($this->user) && $this->token ){
            $this->getUser();
        }    
        return $this;
    }


    public function getdata()
    {
        return $this->__toString();
    }


    public function __toString()
    {
        $o = new \stdClass();
        $o->input = $this->input->__toString();
        $o->posts = $this->posts->__toString();
        $o->gets = $this->gets->__toString();
        $o->files = $this->files->__toString();
        $o->cookies = $this->cookies->__toString();
        $o->sessions = $this->sessions->__toString();
        $o->servers = $this->servers->__toString();
        $o->header = $this->header;
        $o->token = $this->token;
        $o->user = $this->user;
        $o->host = $this->host;
        $o->errorMsg = $this->errorMsg;
        $o->statusCode = $this->statusCode;
        return $o;
    }

    private function getUser() {
        try {
            if($this->token){
                $rs = JwtService::verify($this->token);
                if($rs){
                    $this->verify = true;
                    $this->user = JwtService::getMember();
                } else {
                    $this->user = null; 
                }
            }
        } catch (\Exception $e) { 
            $this->errorMsg = $e->getMessage();
            $this->statusCode = $e->getCode();
            return false;
        } 
    }


    public function __toArray()
    {
        $o = new \stdClass();
        $o->input = $this->input->toArray();
        $o->posts = $this->posts->toArray();
        $o->gets = $this->gets->toArray();
        $o->files = $this->files->toArray();
        $o->cookies = $this->cookies->toArray();
        $o->sessions = $this->sessions->toArray();
        $o->servers = $this->servers->toArray();
        $o->header = $this->header;
        $o->token = $this->token;
        $o->user = $this->user;
        $o->host = $this->host;
        $o->errorMsg = $this->errorMsg;
        $o->statusCode = $this->statusCode;
        return $o;
    }

    private function cookies()
    {
        $o = new Config();
        foreach ($_COOKIE as $key => $value) {
            $o->{$key} = $value;
        }
        $this->cookies  = $o;
    }

    private function sessions()
    {
        $o = new Config();
        if(session_status() != PHP_SESSION_NONE){
            foreach ($_SESSION as $key => $value) {
                $o->{$key} = $value;
                if ($key == 'user') {
                    $this->user = json_decode(json_encode($value), $this->jsonAssoc);
                }
            }
        }
        $this->sessions  = $o;
    }

    private function servers()
    {
        $o = new Config();
        foreach ($_SERVER as $key => $value) {
            $o->{$key} = $value;
        }
        $protocal = explode('/',$_SERVER['SERVER_PROTOCOL'])[0]=='HTTP'?'http':'https';
        $this->host = $protocal.'://'.$_SERVER['HTTP_HOST'];
        $this->servers  = $o;
    }


    private function input()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, $this->jsonAssoc);
        // dump($data);
        $this->input = new Config($data);
    }

    private function inputpost()
    {
        $o = new Config();
        foreach ($_POST as $key => $value) {
            $o->{$key} = filter_input(INPUT_POST, $key);
        }
        $this->posts = $o;
    }

    public function inputget()
    {
        $o = new Config();
        foreach ($_GET as $key => $value) {
            if(gettype($_GET[$key]) == 'array'){
                $o->{$key} = filter_input(INPUT_GET, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            } else {
                $o->{$key} = filter_input(INPUT_GET, $key);
            }
        }
        $this->gets = $o;
    }


    public function inputfiles()
    {
        $o = new Config();
        foreach ($_FILES as $key => $value) {
            $o->{$key} = $value;
        }
        $this->files = $o;
    }

    public function header()
    {
        $headers = getallheaders();
        $this->header = new Config();
        foreach ($headers as $key => $value) {
            $this->header->{$key} = $value;
        }
        $this->token =  $this->getBearerToken();
    }

    /**
     * Get hearder Authorization
     * */
    private function getAuthorizationHeader()
    {
            $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
            return $headers;
    }

    /**
     * get access token from header
     * */
    private function getBearerToken()
    {

        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        // /Bearer\s((.*)\.(.*)\.(.*))/
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        } else {
            if (isset($_COOKIE['ref_token'])) {
                return $_COOKIE['ref_token'];
            }
            if ($this->gets->ref_token) {
                return $this->gets->ref_token;
            }
            if ($this->posts->ref_token) {
                return $this->posts->ref_token;
            }
            if ($this->input->ref_token) {
                return $this->input->ref_token;
            }

            if(isset($_SESSION['ref_token']) &&  $_SESSION['ref_token']) {
                return $_SESSION['ref_token'];
            }
        }
        return null;
    }

    
}