<?php
// student_add.php
require __DIR__ . '/db.php'; // gir $conn og input()

// Hent klasser til nedtrekksliste (uansett request-metode)
$klasser = [];
try {
    $res = $conn->query("SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode");
    while ($row = $res->fetch_assoc()) {
        $klasser[] = $row;
    }
} catch (mysqli_sql_exception $e) {
    $listeFeil = "Klarte ikke å hente klasser: " . $e->getMessage();
}

// Varsler
$msg = null;
$err = null;

// Sticky values
$bn = $fn = $en = $kk = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Les input med din input()-funksjon
    $bn = strtolower((string) input('brukernavn'));
    $fn = (string) input('fornavn');
    $en = (string) input('etternavn');
    $kk = strtoupper((string) input('klassekode'));

    // 1) Påkrevde felter
    if (!$bn || !$fn || !$en || !$kk) {
        $err = "Alle felter må fylles ut.";
    }

    // 2) Regler for brukernavn: 2–7 tegn, a–z/0–9
    if (!$err && !preg_match('/^[a-z0-9]{2,7}$/', $bn)) {
        $err = "Brukernavn må være 2–7 tegn og kun a–z/0–9.";
    }

    // 3) Klassekode må finnes
    if (!$err) {
        $chk = $conn->prepare("SELECT 1 FROM klasse WHERE klassekode = ?");
        $chk->bind_param('s', $kk);
        $chk->execute();
        $chk->store_result();
        $exists = $chk->num_rows > 0;
        $chk->close();
        if (!$exists) {
            $err = "Klassekode «$kk» finnes ikke. Legg den inn under «Legg til klasse».";
        }
    }

    // 4) Sjekk at brukernavn ikke er brukt
    if (!$err) {
        $chk = $conn->prepare("SELECT 1 FROM student WHERE brukernavn = ?");
        $chk->bind_param('s', $bn);
        $chk->execute();
        $chk->store_result();
        $taken = $chk->num_rows > 0;
        $chk->close();
        if ($taken) {
            $err = "Brukernavnet «$bn» finnes allerede. Velg et annet.";
        }
    }

    // 5) Sett inn
    if (!$err) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO student (brukernavn, fornavn, etternavn, klassekode)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('ssss', $bn, $fn, $en, $kk);
            $stmt->execute();
            $stmt->close();

            $msg = "Student «$bn» ble lagt til i klasse «$kk».";
            // Nullstill feltene etter suksess
            $bn = $fn = $en = $kk = '';
        } catch (mysqli_sql_exception $e) {
            // Duplikat, FK-feil o.l.
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
        h2{margin:.2rem 0 1rem}
        nav a{margin-right:1rem;text-decoration:none;color:#0b63c4}
        .notice,.error{padding:.75rem 1rem;border-radius:.5rem;margin:1rem 0}
        .notice{background:#e9f6ff;border:1px solid #b7e0ff}
        .error{background:#ffecec;border:1px solid #ffc3c3}
        label{display:block;margin-top:.9rem;font-weight:600}
        input,select,button{width:100%;max-width:420px;padding:.6rem;border:1px solid #ccc;border-radius:.5rem}
        button{margin-top:1rem;cursor:pointer}
        small{color:#444}
    </style>
</head>
<body>
    <h2>Legg til student</h2>
    <nav>
        <a href="index.php">Hjem</a>
        <a href="student_list.php">Vis studenter</a>
    </nav>

    <?php if (!empty($listeFeil)): ?>
        <div class="error"><?= htmlspecialchars($listeFeil) ?></div>
    <?php endif; ?>

    <?php if ($msg): ?>
        <div class="notice"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if ($err): ?>
        <div class="error"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="post" action="student_add.php" autocomplete="off">
        <label for="bn">Brukernavn <small>(2–7 tegn, a–z/0–9)</small></label>
        <input id="bn" name="brukernavn" type="text" value="<?= htmlspecialchars($bn) ?>" minlength="2" maxlength="7" pattern="[a-z0-9]{2,7}" required>

        <label for="fn">Fornavn</label>
        <input id="fn" name="fornavn" type="text" value="<?= htmlspecialchars($fn) ?>" required>

        <label for="en">Etternavn</label>
        <input id="en" name="etternavn" type="text" value="<?= htmlspecialchars($en) ?>" required>

        <label for="kk">Klassekode</label>
        <select id="kk" name="klassekode" required>
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

