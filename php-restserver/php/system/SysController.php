<?php

use Illuminate\Database\Capsule\Manager as Capsule;
//line notify

class SysController extends BaseController
{


    /**
     * @noAuth
     * @url GET /routes
     */
    public function getRoutes()
    {
        $url = $_SERVER['HTTP_HOST'] . $this->server->root . '/';
        $url = replaceslag($url);
        if ($this->server->mode == 'debug') {
            echo '<style> .divline { width:100%; text-align:center; border-bottom: 1px dashed #000; line-height:0.1em; margin:10px 0 20px; color: white;border-block-color: inherit; } 
            a:link {  color: white;  background-color: transparent;  text-decoration: none;}a:visited {  color: white;  background-color: transparent;  text-decoration: none;}a:hover {  color: white;  background-color: transparent;  text-decoration: underline;}a:active {  color: white;  background-color: transparent;  text-decoration: underline;}body {  background-color:  #0a420a;  color: white;}
            </style><center><table><thead><tr><td><b>Route</b></td><td><b>Controller</b></td><td><b>Method</b></td><td><b>$args</b></td><td>null</td><td><b>@ noAuth</b></td></tr></thead><tbody>';
            foreach ($this->server->map as $routekey => $routes) {
                echo '<tr><td colspan="6"><div style="color:white;display:flex;padding-right:10px;height:15px;">
                <div class="divline" style="width:200px;">&nbsp;</div>
                <span style="white-space: pre;">&nbsp;>&nbsp;@url ' . $routekey . '&nbsp;</span>
                <div class="divline">&nbsp;</div></div></td></tr>';
                switch ($routekey) {
                    case 'GET':
                        foreach ($routes as $key => $value) {
                            echo "<tr><td>" . ($routekey == 'GET' ? '<a href="http://' . $url . $key . '">' . (empty($key) ? '/' : $key) . '</a>'    : $key) . "</td><td>$value[0]</td><td>$value[1]</td><td><pre>" . json_encode($value[2]) . "</pre></td><td>" . json_encode($value[3]) . "</td><td>" . json_encode($value[4]) . "</td></tr>";
                        }
                        break;
                    case 'POST':
                    case 'OPTIONS':
                    default:
                        foreach ($routes as $key => $value) {
                            echo "<tr><td style='cursor:pointer;' onclick='alert(\"" . $key . "\")'>$key</td><td>$value[0]</td><td>$value[1]</td><td><pre>" . json_encode($value[2]) . "</pre></td><td>" . json_encode($value[3]) . "</td><td>" . json_encode($value[4]) . "</td></tr>";
                        }
                        break;
                }
            }
            echo '<tr><td colspan="6"><div style="display:flex;padding-right:10px;height:15px;">
            <div class="divline">&nbsp;</div><span style="white-space: pre;">&nbsp;>&nbsp;END.&nbsp;</span>
            </div></td></tr></tbody></table></center>';
        }
        exit(0);
    }
}
