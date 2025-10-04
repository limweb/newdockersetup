<?php
$serverName = "mssql"; // ชื่อ service ใน docker-compose
$connectionOptions = [
    "Database" => "master",
    "Uid" => "sa",
    "PWD" => "YourStrong@Passw0rd",
    "Encrypt" => true,
    "TrustServerCertificate" => true
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    echo "เชื่อมต่อ MSSQL สำเร็จ!";
} else {
    die(print_r(sqlsrv_errors(), true));
}
