<?php

namespace App\Utils;

use Jacwright\RestServer\RestServer as BaseRestServer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use ServicesJSON;
use ReflectionObject;
use ReflectionClass;

class RestServer extends BaseRestServer
{
	/** @var array */
	public $map = [];

	/** @var bool */
	public $useCors = true;

	/** @var string */
	private $charset = 'utf-8';

	/** @var \Illuminate\Database\Capsule\Manager */
	public $capsule;

	/** @var \stdClass */
	public $config;

	public function __construct()
	{
		parent::__construct();
		$this->charset = 'utf-8';
	}

	public function setCharset($charset)
	{
		$this->charset = $charset;
	}

	//text/plain
	//text/html
	//application/json
	//application/xml
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @param prefix
	 * @param dbname
	 * @param host
	 * @param username
	 * @param password
	 */

	public function setConnection($dbname = null, $prefix = '', $host = null, $username = null, $password = null, $charset = 'utf8', $collation = 'utf8_unicode_ci', $connection = 'default')
	{
		// for new and reset config
		$config = $this->config->dbconfig;
		$config['database'] = ($dbname ?: $config['database']);
		$config['prefix'] = ($prefix ?: $config['prefix']);
		$config['host'] = ($host ?: $config['host']);
		$config['username'] = ($username ?: $config['username']);
		$config['password'] = ($password ?: $config['password']);
		$config['charset'] = $charset;
		$config['collation'] = $collation;
		$this->capsule = new Capsule;
		$this->capsule->addConnection($config, $connection);
		$this->capsule->setEventDispatcher(new Dispatcher(new Container));
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();
		$this->config->dbconfig = $config;
		// Capsule::setTablePrefix($prefix);
		// echo Capsule::getTablePrefix();
		// Capsule::setTablePrefix('sys_');
		// echo Capsule::getTablePrefix();
		// $this->server->setconnection() use in controller
	}
	public function sendData($data)
	{
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: 0");
		header('Content-Type: ' . $this->format . '; charset=' . $this->charset);
		if ($this->useCors) {
			$this->corsHeaders();
		}

		if ($this->format == \Jacwright\RestServer\RestFormat::XML) {
			if (is_object($data) && method_exists($data, '__keepOut')) {
				$data = clone $data;
				foreach ($data->__keepOut() as $prop) {
					unset($data->$prop);
				}
			}

			$this->xml_encode($data);
		} else {
			if (is_object($data) && method_exists($data, '__keepOut')) {
				$data = clone $data;
				foreach ($data->__keepOut() as $prop) {
					unset($data->$prop);
				}
			}

			$options = 0;
			if ($this->mode == 'debug' && defined('JSON_PRETTY_PRINT')) {
				$options = JSON_PRETTY_PRINT;
			}

			if (defined('JSON_UNESCAPED_UNICODE')) {
				$options = $options | JSON_UNESCAPED_UNICODE;
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE) ?: (new ServicesJSON())->encode($data);
		}
	}
	private function corsHeaders()
	{
		// to support multiple origins we have to treat origins as an array
		$allowedOrigin = (array)$this->allowedOrigin;
		// if no origin header is present then requested origin can be anything (i.e *)
		$currentOrigin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		if (in_array($currentOrigin, $allowedOrigin)) {
			$allowedOrigin = array($currentOrigin); // array ; if there is a match then only one is enough
		}
		foreach ($allowedOrigin as $allowed_origin) { // to support multiple origins
			header("Access-Control-Allow-Origin: $allowed_origin");
		}
		header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
		header('Access-Control-Allow-Credential: true');
		header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, Authorization');
	}


	/**
	 * $config =  array of config
	 * $connection  string of nameconnect ex  dba  dbb dbc
	 */
	public function addConnection($config, $connection = 'default')
	{
		if ($this->capsule && $config) {
			if ($connection == 'default') {
				$this->capsule = new Capsule();
			}
			$this->capsule->addConnection($config, $connection);
			$this->capsule->setEventDispatcher(new Dispatcher(new Container));
			$this->capsule->setAsGlobal();
			$this->capsule->bootEloquent();
			$this->config->{$connection}  = $config;
		}
	}

	public function handleError($statusCode, $errorMessage = null)
	{
		if ($statusCode == 404) {
			include SRVPATH . '/dist/index.php';
		} else {
			$method = "handle$statusCode";

			foreach ($this->errorClasses as $class) {
				if (is_object($class)) {
					$reflection = new ReflectionObject($class);
				} else if (class_exists($class)) {
					$reflection = new ReflectionClass($class);
				}

				if (isset($reflection)) {
					if ($reflection->hasMethod($method)) {
						$obj = is_string($class) ? new $class() : $class;
						$obj->$method();
						return;
					}
				}
			}

			if (!$errorMessage) {
				$errorMessage = $this->codes[$statusCode];
			}

			$this->setStatus($statusCode);
			$this->sendData(array('error' => array('code' => $statusCode, 'message' => $errorMessage)));
		}
	}

	private $codes = array(
		'100' => 'Continue',
		'200' => 'OK',
		'201' => 'Created',
		'202' => 'Accepted',
		'203' => 'Non-Authoritative Information',
		'204' => 'No Content',
		'205' => 'Reset Content',
		'206' => 'Partial Content',
		'300' => 'Multiple Choices',
		'301' => 'Moved Permanently',
		'302' => 'Found',
		'303' => 'See Other',
		'304' => 'Not Modified',
		'305' => 'Use Proxy',
		'307' => 'Temporary Redirect',
		'400' => 'Bad Request',
		'401' => 'Unauthorized',
		'402' => 'Payment Required',
		'403' => 'Forbidden',
		'404' => 'Not Found',
		'405' => 'Method Not Allowed',
		'406' => 'Not Acceptable',
		'409' => 'Conflict',
		'410' => 'Gone',
		'411' => 'Length Required',
		'412' => 'Precondition Failed',
		'413' => 'Request Entity Too Large',
		'414' => 'Request-URI Too Long',
		'415' => 'Unsupported Media Type',
		'416' => 'Requested Range Not Satisfiable',
		'417' => 'Expectation Failed',
		'500' => 'Internal Server Error',
		'501' => 'Not Implemented',
		'503' => 'Service Unavailable'
	);

	private function xml_encode($mixed, $domElement = null, $DOMDocument = null)
	{  //@todo add type hint for $domElement and $DOMDocument
		if (is_null($DOMDocument)) {
			$DOMDocument = new DOMDocument;
			$DOMDocument->formatOutput = true;
			$this->xml_encode($mixed, $DOMDocument, $DOMDocument);
			echo $DOMDocument->saveXML();
		} else if (is_null($mixed) || $mixed === false || (is_array($mixed) && empty($mixed))) {
			$domElement->appendChild($DOMDocument->createTextNode(null));
		} else if (is_array($mixed)) {
			foreach ($mixed as $index => $mixedElement) {
				if (is_int($index)) {
					if ($index === 0) {
						$node = $domElement;
					} else {
						$node = $DOMDocument->createElement($domElement->tagName);
						$domElement->parentNode->appendChild($node);
					}
				} else {
					$index = str_replace(' ', '_', $index);
					$plural = $DOMDocument->createElement($index);
					$domElement->appendChild($plural);
					$node = $plural;

					if (!(rtrim($index, 's') === $index) && !empty($mixedElement)) {
						$singular = $DOMDocument->createElement(rtrim($index, 's'));
						$plural->appendChild($singular);
						$node = $singular;
					}
				}

				$this->xml_encode($mixedElement, $node, $DOMDocument);
			}
		} else {
			$domElement->appendChild($DOMDocument->createTextNode($mixed));
		}
	}
}
