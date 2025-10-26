<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$defaultHost = "localhost";
$defaultUser = "root";
$defaultPass = "root";
$defaultName = "PRG120";



$host = getenv('DB_HOST') ?: $defaultHost;
$user = getenv('DB_USER') ?: $defaultUser;
$pass = getenv('DB_PASS') ?: $defaultPass;
$name = getenv('DB_NAME') ?: $defaultName;

$conn = new mysqli($host, $user, $pass, $name);
if ($conn->connect_error) {
    die("Tilkoblingsfeil: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");


function input($key, $source = 'POST') {
    $arr = $source === 'GET' ? $_GET : $_POST;
    return isset($arr[$key]) ? trim($arr[$key]) : null;
}
?>
