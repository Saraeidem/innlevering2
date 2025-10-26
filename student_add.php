<?php
require 'db.php';

$msg = $err = null;

// Hent klasser til nedtrekksliste først
$klasser = $conn->query("SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode")->fetch_all(MYSQLI_ASSOC);

// Når skjema postes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bn = strtolower(input('brukernavn'));     // 2–7 tegn, a–z/0–9
    $fn = input('fornavn');
    $en = input('etternavn');
    $kk = strtoupper(input('klassekode'));

    // 1) Felt må finnes
    if (!$bn || !$fn || !$en || !$kk) {
        $err = "Alle felter må fylles ut.";
    }

    // 2) Brukernavn-regler
    if (!$err && !preg_match('/^[a-z0-9]{2,7}$/', $bn)) {
        $err = "Brukernavn må være 2–7 tegn og bare a–z/0–9.";
    }

    // 3) Klassekode må finnes
    if (!$err) {
        $chk = $conn->prepare("SELECT 1 FROM klasse WHERE klassekode=?");
        $chk->bind_param("s", $kk);
        $chk->execute();
        $exists = $chk->get_result()->num_rows > 0;
        $chk->close();
        if (!$exists) {
            $err = "Klassekode $kk finnes ikke. Legg den til under «Legg til klasse».";
        }
    }

    // 4) Prøv å sette inn
    if (!$err) {
        try {
            $stmt = $conn->prepare("INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $bn, $fn, $en, $kk);
            $stmt->execute();
            $stmt->close();
            $msg = "Student «$bn» ble lagt til i klasse «$kk».";
        } catch (mysqli_sql_exception $e) {
            // Vanlige årsaker: duplikat primærnøkkel (samme brukernavn)
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $err = "Brukernavn «$bn» finnes allerede. Velg et annet.";
            } else {
                $err = "Databasefeil: " . $e->getMessage(); // synlig pga mysqli_report
            }
        }
    }
}
?>
<!doctype html>
<html lang="no">
<head>
  <meta charset="utf-8">
  <title>Legg til student</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Legg til student</h2>
  <nav>
    <a href="index.php">Hjem</a>
    <a href="student_list.php">Vis studenter</a>
  </nav>

  <?php if ($msg): ?><div class="notice"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <form method="post" autocomplete="off">
    <label>Brukernavn (2–7 tegn, a–z/0–9)</label>
    <input type="text" name="brukernavn" maxlength="7" required>

    <label>Fornavn</label>
    <input type="text" name="fornavn" required>

    <label>Etternavn</label>
    <input type="text" name="etternavn" required>

    <label>Klassekode</label>
    <select name="klassekode" required>
      <option value="">Velg klasse…</option>
      <?php foreach ($klasser as $k): ?>
        <option value="<?= htmlspecialchars($k['klassekode']) ?>">
          <?= htmlspecialchars($k['klassekode']) ?> – <?= htmlspecialchars($k['klassenavn']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <p><button type="submit">Lagre</button></p>
  </form>
</body>
</html>
