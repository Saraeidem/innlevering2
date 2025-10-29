<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ==== DB-tilkobling ==== */
$host   = 'b-studentsql-1.usn.no';
$user   = 'saeid8969';
$pass   = 'ad3seaeid8969';   // ditt ekte passord
$dbname = 'saeid8969';
$port   = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
$conn->set_charset('utf8mb4');

/* ==== Hjelpefunksjon brukt i alle sidene ==== */
function input($key, $src = 'POST') {
    $a = ($src === 'GET') ? $_GET : $_POST;
    return isset($a[$key]) ? trim($a[$key]) : null;
}
