<?php
require 'db.php';
$bn = isset($_GET['brukernavn']) ? $_GET['brukernavn'] : null;

if ($bn) {
    $stmt = $conn->prepare("DELETE FROM student WHERE brukernavn=?");
    $stmt->bind_param("s", $bn);
    $stmt->execute();
    $stmt->close();
}
header("Location: student_list.php");
