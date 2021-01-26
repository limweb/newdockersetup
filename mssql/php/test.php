<?php
$dsn = "sqlsrv:Server=mssql,1433;";
$username = "sa";
$password = "mssql2017@pass";
try {
    $conn = new PDO($dsn,$username,$password);
    if($conn){
        echo 'Connected!';
    } else {
        var_dump($conn);
    }
} catch (PDOException $e) {
    echo ("Error connecting to SQL Server: " . $e->getMessage());
}