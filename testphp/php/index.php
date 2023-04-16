<?php

$servers = $_SERVER;
echo '<h1>Server</h1></br>';
foreach($servers as $key => $val ) {
    echo $key,'----->',$val,'<br/>';
}
echo 'End.';
phpinfo();