<?php
require 'db.php';

$msg = $err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = strtoupper(input('klassekode'));
    $navn = input('klassenavn');
    $stud = input('studiumkode');

    if (!$kode || !$navn || !$stud) {
        $err = "Alle felter må fylles ut.";
    } elseif (!preg_match('/^[A-Z0-9]{2,5}$/', $kode)) {
        $err = "Klassekode må være 2–5 tegn, A–Z/0–9.";
    } else {
        $stmt = $conn->prepare("INSERT INTO klasse(klassekode, klassenavn, studiumkode) VALUES(?,?,?)");
        $stmt->bind_param("sss", $kode, $navn, $stud);
        if ($stmt->execute()) {
            $msg = "Klasse $kode lagt til.";
        } else {
            $err = "Feil: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="no">
<head>
  <meta charset="utf-8">
  <title>Legg til klasse</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Legg til klasse</h2>
  <nav>
    <a href="index.php">Hjem</a>
    <a href="klasse_list.php">Vis klasser</a>
  </nav>

  <?php if ($msg): ?><div class="notice"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <form method="post">
    <label>Klassekode</label>
    <input type="text" name="klassekode" maxlength="5" required>
    <label>Klassenavn</label>
    <input type="text" name="klassenavn" required>
    <label>Studiumkode</label>
    <input type="text" name="studiumkode" required>
    <p><button type="submit">Lagre</button></p>
  </form>
</body>
</html>
