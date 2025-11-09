<?php
require __DIR__ . '/db.php'; // gir $conn og input()

// Varsler
$msg = null;
$err = null;

// Sticky values
$bn = $fn = $en = $kk = '';

// Hent klasser til nedtrekksliste (uansett)
$klasser = [];
try {
    $res = $conn->query("SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode");
    while ($row = $res->fetch_assoc()) {
        $klasser[] = $row;
    }
} catch (mysqli_sql_exception $e) {
    $err = "Klarte ikke å hente klasser: " . $e->getMessage();
}

// Skjema postet?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Hent input
    $bn = strtolower((string) input('brukernavn'));
    $fn = (string) input('fornavn');
    $en = (string) input('etternavn');
    $kk = strtoupper((string) input('klassekode'));

    // 1) Påkrevde felter
    if (!$bn || !$fn || !$en || !$kk) {
        $err = "Alle felter må fylles ut.";
    }

    // 2) Brukernavn-regler: 2–7 tegn, a–z/0–9
    if (!$err && !preg_match('/^[a-z0-9]{2,7}$/', $bn)) {
        $err = "Brukernavn må være 2–7 tegn og kun a–z/0–9.";
    }

    // 3) Sjekk at klassekode finnes
    if (!$err) {
        $chk = $conn->prepare("SELECT 1 FROM klasse WHERE klassekode = ?");
        $chk->bind_param('s', $kk);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows === 0) {
            $err = "Klassekode «$kk» finnes ikke. Legg den inn under «Legg til klasse».";
        }
        $chk->close();
    }

    // 4) Sjekk at brukernavnet ikke allerede finnes
    if (!$err) {
        $chk = $conn->prepare("SELECT 1 FROM student WHERE brukernavn = ?");
        $chk->bind_param('s', $bn);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            $err = "Brukernavnet «$bn» finnes allerede.";
        }
        $chk->close();
    }

    // 5) Sett inn student
    if (!$err) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO student (brukernavn, fornavn, etternavn, klassekode)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('ssss', $bn, $fn, $en, $kk);
            $stmt->execute();
            $stmt->close();
           

            <?php if ($msg): ?>
  <div class="notice"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>


        } catch (mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                $err = "Brukernavnet «$bn» finnes allerede.";
            } else {
                $err = "Databasefeil: " . $e->getMessage();
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:760px;margin:2rem auto;padding:0 1rem;line-height:1.45}
        nav a{margin-right:1rem;color:#0b63c4;text-decoration:none}
        .notice,.error{padding:.75rem 1rem;border-radius:.5rem;margin:1rem 0}
        .notice{background:#e8f6ff;border:1px solid #b9e4ff}
        .error{background:#ffecec;border:1px solid #ffb8b8}
        label{display:block;margin-top:.9rem;font-weight:600}
        input,select,button{width:100%;max-width:420px;padding:.6rem;border:1px solid #ccc;border-radius:.5rem}
        button{cursor:pointer;margin-top:1rem}
        small{color:#444}
    </style>
</head>
<body>

<h2>Legg til student</h2>
<nav>
    <a href="index.php">Hjem</a>
    <a href="student_list.php">Vis studenter</a>
</nav>

<?php if ($msg): ?>
    <div class="notice"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($err): ?>
    <div class="error"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<form method="post" autocomplete="off">
    <label>Brukernavn <small>(2–7 tegn, a–z/0–9)</small></label>
    <input type="text" name="brukernavn" value="<?= htmlspecialchars($bn) ?>" minlength="2" maxlength="7" pattern="[a-z0-9]{2,7}" required>

    <label>Fornavn</label>
    <input type="text" name="fornavn" value="<?= htmlspecialchars($fn) ?>" required>

    <label>Etternavn</label>
    <input type="text" name="etternavn" value="<?= htmlspecialchars($en) ?>" required>

    <label>Klassekode</label>
    <select name="klassekode" required>
        <option value="">Velg klasse…</option>
        <?php foreach ($klasser as $k): ?>
            <?php
                $kode = $k['klassekode'];
                $navn = $k['klassenavn'] ?? '';
                $sel  = ($kk === $kode) ? 'selected' : '';
            ?>
            <option value="<?= htmlspecialchars($kode) ?>" <?= $sel ?>>
                <?= htmlspecialchars($kode) ?><?= $navn ? ' – ' . htmlspecialchars($navn) : '' ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Lagre</button>
</form>

</body>
</html>


