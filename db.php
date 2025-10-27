<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* --- KOBLING TIL SKOLENS DATABASE (DOKPLOY) --- */
$host = "b-studentsql-1.usn.no";   // fra SelfService
$user = "saeid8969";               // fra SelfService
$pass = "DittPassordHer";          // fra SelfService
$dbname = "saeid8969";             // fra SelfService
$port = 3306;                      // som regel 3306

$conn = new mysqli($host, $user, $pass, $dbname, $port);
$conn->set_charset("utf8mb4");

/* valgfri hjelpefunksjon */
function input($key, $src='POST') {
  $a = $src === 'GET' ? $_GET : $_POST;
  return isset($a[$key]) ? trim($a[$key]) : null;
}
?>
