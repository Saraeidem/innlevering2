<?php
require 'db.php';
$kode = isset($_GET['klassekode']) ? $_GET['klassekode'] : null;

if ($kode) {
    // Hindre sletting hvis studenter finnes (RESTRICT vil også stoppe, men vi gir pen beskjed)
    $chk = $conn->prepare("SELECT COUNT(*) FROM student WHERE klassekode=?");
    $chk->bind_param("s", $kode);
    $chk->execute();
    $chk->bind_result($cnt);
    $chk->fetch();
    $chk->close();

    if ($cnt > 0) {
        header("Location: klasse_list.php?err=" . urlencode("Kan ikke slette $kode – det finnes studenter i klassen."));
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM klasse WHERE klassekode=?");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $stmt->close();
}
header("Location: klasse_list.php");
