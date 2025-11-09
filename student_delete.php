<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: student_list.php?err=' . urlencode('Ugyldig forespørsel.'));
    exit;
}

$bn = (string) input('brukernavn'); // fra POST

if ($bn === '') {
    header('Location: student_list.php?err=' . urlencode('Mangler brukernavn å slette.'));
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM student WHERE brukernavn = ?");
    $stmt->bind_param('s', $bn);
    $stmt->execute();
    $deleted = $stmt->affected_rows; // hvor mange rader ble slettet?
    $stmt->close();

    if ($deleted > 0) {
        header('Location: student_list.php?msg=' . urlencode("Slettet «$bn»."));
    } else {
        // Ingen rad matchet – brukernavnet fantes ikke
        header('Location: student_list.php?err=' . urlencode("Fant ikke «$bn». Ingenting ble slettet."));
    }
    exit;

} catch (mysqli_sql_exception $e) {
    header('Location: student_list.php?err=' . urlencode('Databasefeil: ' . $e->getMessage()));
    exit;
}
