<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ==== KOBLING TIL SKOLENS DATABASE ==== */
$host   = 'b-studentsql-1.usn.no';
$user   = 'saeid8969';
$pass   = 'ad3seaeid8969';   // sett ditt faktiske passord
$dbname = 'saeid8969';
$port   = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
$conn->set_charset('utf8mb4');

/* ==== HJELPEFUNKSJON SOM SIDENE DINE BRUKER ==== */
function input($key, $src = 'POST') {
    $a = ($src === 'GET') ? $_GET : $_POST;
    return isset($a[$key]) ? trim($a[$key]) : null;
}
