<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host   = 'b-studentsql-1.usn.no'; // fra SelfService
$user   = 'saeid8969';             // fra SelfService
$pass   = 'ad3esaeid8969';     // fra SelfService
$dbname = 'saeid8969';             // fra SelfService
$port   = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
$conn->set_charset('utf8mb4');
