<?php
define('ServicesJSON_SLICE',   1);
define('ServicesJSON_IN_STR',  2);
define('ServicesJSON_IN_ARR',  3);
define('ServicesJSON_IN_OBJ',  4);
define('ServicesJSON_IN_CMT', 5);
define('ServicesJSON_LOOSE_TYPE', 16);
define('ServicesJSON_SUPPRESS_ERRORS', 32);
class ServicesJSON {
    private $use;
    function __construct($use = 0)
    {
        $this->use = $use;
    }
    function utf162utf8($utf16)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }
        $bytes = (ord($utf16[0]) << 8) | ord($utf16[1]);
        switch (true) {
            case ((0x7F & $bytes) == $bytes):
                return chr(0x7F & $bytes);
            case (0x07FF & $bytes) == $bytes:
                return chr(0xC0 | (($bytes >> 6) & 0x1F))
                    . chr(0x80 | ($bytes & 0x3F));
            case (0xFFFF & $bytes) == $bytes:
                return chr(0xE0 | (($bytes >> 12) & 0x0F))
                    . chr(0x80 | (($bytes >> 6) & 0x3F))
                    . chr(0x80 | ($bytes & 0x3F));
        }
        return '';
    }
    function encode($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'NULL':
                return 'null';
            case 'integer':
                return (int) $var;
            case 'double':
            case 'float':
                return (float) $var;
            case 'string':
                $ascii = '';
                if (preg_match("/\"/", $var)) {
                    $var = preg_replace('/"/', '\"', $var);
                }
                $var = preg_replace("/[\n\r]/", "", $var);
                $ascii .= $var;
                return '"' . $ascii . '"';
            case 'array':
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
                    $properties = array_map([$this, 'name_value'], array_keys($var), array_values($var));
                    foreach ($properties as $property) {
                        if (ServicesJSON::isError($property)) {
                            return $property;
                        }
                    }
                    return '{' . join(',', $properties) . '}';
                }
                $elements = array_map([$this, 'encode'], $var);
                foreach ($elements as $element) {
                    if (ServicesJSON::isError($element)) {
                        return $element;
                    }
                }
                return '[' . join(',', $elements) . ']';
            case 'object':
                $vars = get_object_vars($var);
                $properties = array_map([$this, 'name_value'], array_keys($vars), array_values($vars));
                foreach ($properties as $property) {
                    if (ServicesJSON::isError($property)) {
                        return $property;
                    }
                }
                return '{' . join(',', $properties) . '}';
            default:
                return ($this->use & ServicesJSON_SUPPRESS_ERRORS)
                    ? 'null'
                    : new ServicesJSON_Error(gettype($var) . " can not be encoded as JSON string");
        }
    }
    function name_value($name, $value)
    {
        $encoded_value = $this->encode($value);
        if (ServicesJSON::isError($encoded_value)) {
            return $encoded_value;
        }
        return $this->encode(strval($name)) . ':' . $encoded_value;
    }
    function reduce_string($str)
    {
        $str = preg_replace([
            '#^\s*//(.+)$#m',
            '#^\s*/\*(.+)\*/#Us',
            '#/\*(.+)\*/\s*$#Us'
        ], '', $str);
        return trim($str);
    }
    function decode($str)
    {
        $str = $this->reduce_string($str);
        switch (strtolower($str)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
            default:
                $m = [];
                if (is_numeric($str)) {
                    return ((float)$str == (int)$str)
                        ? (int)$str
                        : (float)$str;
                } elseif (preg_match('/^("|\').*(\1)$/s', $str, $m) && $m[1] == $m[2]) {
                    $delim = substr($str, 0, 1);
                    $chrs = substr($str, 1, -1);
                    $utf8 = '';
                    $strlen_chrs = strlen($chrs);
                    for ($c = 0; $c < $strlen_chrs; ++$c) {
                        $substr_chrs_c_2 = substr($chrs, $c, 2);
                        $ord_chrs_c = ord($chrs[$c]);
                        switch (true) {
                            case $substr_chrs_c_2 == '\b':
                                $utf8 .= chr(0x08);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\t':
                                $utf8 .= chr(0x09);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\n':
                                $utf8 .= chr(0x0A);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\f':
                                $utf8 .= chr(0x0C);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\r':
                                $utf8 .= chr(0x0D);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\\"':
                            case $substr_chrs_c_2 == '\\\'':
                            case $substr_chrs_c_2 == '\\\\':
                            case $substr_chrs_c_2 == '\\/':
                                if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
                                    ($delim == "'" && $substr_chrs_c_2 != '\\"')
                                ) {
                                    $utf8 .= $chrs[++$c];
                                }
                                break;
                            case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
                                $utf16 = chr(hexdec(substr($chrs, ($c + 2), 2)))
                                    . chr(hexdec(substr($chrs, ($c + 4), 2)));
                                $utf8 .= $this->utf162utf8($utf16);
                                $c += 5;
                                break;
                            case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
                                $utf8 .= $chrs[$c];
                                break;
                            case ($ord_chrs_c & 0xE0) == 0xC0:
                                $utf8 .= substr($chrs, $c, 2);
                                ++$c;
                                break;
                            case ($ord_chrs_c & 0xF0) == 0xE0:
                                $utf8 .= substr($chrs, $c, 3);
                                $c += 2;
                                break;
                            case ($ord_chrs_c & 0xF8) == 0xF0:
                                $utf8 .= substr($chrs, $c, 4);
                                $c += 3;
                                break;
                            case ($ord_chrs_c & 0xFC) == 0xF8:
                                $utf8 .= substr($chrs, $c, 5);
                                $c += 4;
                                break;
                            case ($ord_chrs_c & 0xFE) == 0xFC:
                                $utf8 .= substr($chrs, $c, 6);
                                $c += 5;
                                break;
                        }
                    }
                    return $utf8;
                } elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {
                    if ($str[0] == '[') {
                        $stk = [ServicesJSON_IN_ARR];
                        $arr = [];
                    } else {
                        if ($this->use & ServicesJSON_LOOSE_TYPE) {
                            $stk = [ServicesJSON_IN_OBJ];
                            $obj = [];
                        } else {
                            $stk = [ServicesJSON_IN_OBJ];
                            $obj = new stdClass();
                        }
                    }
                    array_push($stk, ['what'  => ServicesJSON_SLICE, 'where' => 0, 'delim' => false]);
                    $chrs = substr($str, 1, -1);
                    $chrs = $this->reduce_string($chrs);
                    if ($chrs == '') {
                        if (reset($stk) == ServicesJSON_IN_ARR) {
                            return $arr;
                        } else {
                            return $obj;
                        }
                    }
                    $strlen_chrs = strlen($chrs);
                    for ($c = 0; $c <= $strlen_chrs; ++$c) {
                        $top = end($stk);
                        $substr_chrs_c_2 = substr($chrs, $c, 2);
                        if (($c == $strlen_chrs) || (($chrs[$c] == ',') && ($top['what'] == ServicesJSON_SLICE))) {
                            $slice = substr($chrs, $top['where'], ($c - $top['where']));
                            array_push($stk, array('what' => ServicesJSON_SLICE, 'where' => ($c + 1), 'delim' => false));
                            if (reset($stk) == ServicesJSON_IN_ARR) {
                                array_push($arr, $this->decode($slice));
                            } elseif (reset($stk) == ServicesJSON_IN_OBJ) {
                                $parts = [];
                                if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    $key = $this->decode($parts[1]);
                                    $val = $this->decode($parts[2]);
                                    if ($this->use & ServicesJSON_LOOSE_TYPE) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                } elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    $key = $parts[1];
                                    $val = $this->decode($parts[2]);
                                    if ($this->use & ServicesJSON_LOOSE_TYPE) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                }
                            }
                        } elseif ((($chrs[$c] == '"') || ($chrs[$c] == "'")) && ($top['what'] != ServicesJSON_IN_STR)) {
                            array_push($stk, ['what' => ServicesJSON_IN_STR, 'where' => $c, 'delim' => $chrs[$c]]);
                        } elseif (($chrs[$c] == $top['delim']) &&
                            ($top['what'] == ServicesJSON_IN_STR) &&
                            ((strlen(substr($chrs, 0, $c)) - strlen(rtrim(substr($chrs, 0, $c), '\\'))) % 2 != 1)
                        ) {
                            array_pop($stk);
                        } elseif (($chrs[$c] == '[') &&
                            in_array($top['what'], [ServicesJSON_SLICE, ServicesJSON_IN_ARR, ServicesJSON_IN_OBJ])
                        ) {
                            array_push($stk, ['what' => ServicesJSON_IN_ARR, 'where' => $c, 'delim' => false]);
                        } elseif (($chrs[$c] == ']') && ($top['what'] == ServicesJSON_IN_ARR)) {
                            array_pop($stk);
                        } elseif (($chrs[$c] == '{') &&
                            in_array($top['what'], array(ServicesJSON_SLICE, ServicesJSON_IN_ARR, ServicesJSON_IN_OBJ))
                        ) {
                            array_push($stk, array('what' => ServicesJSON_IN_OBJ, 'where' => $c, 'delim' => false));
                        } elseif (($chrs[$c] == '}') && ($top['what'] == ServicesJSON_IN_OBJ)) {
                            array_pop($stk);
                        } elseif (($substr_chrs_c_2 == '/*') &&
                            in_array($top['what'], array(ServicesJSON_SLICE, ServicesJSON_IN_ARR, ServicesJSON_IN_OBJ))
                        ) {
                            array_push($stk, array('what' => ServicesJSON_IN_CMT, 'where' => $c, 'delim' => false));
                            $c++;
                        } elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == ServicesJSON_IN_CMT)) {
                            array_pop($stk);
                            $c++;
                            for ($i = $top['where']; $i <= $c; ++$i)
                                $chrs = substr_replace($chrs, ' ', $i, 1);
                        }
                    }
                    if (reset($stk) == ServicesJSON_IN_ARR) {
                        return $arr;
                    } elseif (reset($stk) == ServicesJSON_IN_OBJ) {
                        return $obj;
                    }
                }
        }
    }
    function isError($data, $code = null)
    {
        if (class_exists('pear')) {
            return PEAR::isError($data, $code);
        } elseif (is_object($data) && (get_class($data) == 'ServicesJSON_error' || is_subclass_of($data, 'ServicesJSON_error'))) {
            return true;
        }
        return false;
    }
}

if (class_exists('PEAR_Error')) {
    class ServicesJSON_Error extends PEAR_Error
    {
        function __construct($message = 'unknown error', $code = null, $mode = null, $options = null, $userinfo = null)
        {
            parent::PEAR_Error($message, $code, $mode, $options, $userinfo);
        }
    }
} else {
    class ServicesJSON_Error
    {
        function __construct($message = 'unknown error', $code = null, $mode = null, $options = null, $userinfo = null) {}
    }
}
