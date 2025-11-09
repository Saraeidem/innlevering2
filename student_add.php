<?php
// student_add.php
require __DIR__ . '/db.php'; // gir $conn og input()

// --- Init (hindrer "Undefined variable") ---
$msg = null;
$err = null;
$bn = $fn = $en = $kk = '';

// --- Hent klasser til dropdown uansett ---
$klasser = [];
try {
    $res = $conn->query("SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode");
    while ($row = $res->fetch_assoc()) {
        $klasser[] = $row;
    }
} catch (mysqli_sql_exception $e) {
    $err = "Klarte ikke å hente klasser: " . $e->getMessage();
}

// --- Håndter innsending ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Les input via input()
    $bn = strtolower((string) input('brukernavn'));
    $fn = (string) input('fornavn');
    $en = (string) input('etternavn');
    $kk = strtoupper((string) input('klassekode'));

    // Påkrevde felt
    if (!$bn || !$fn || !$en || !$kk) {
        $err = "Alle felter må fylles ut.";
    }

    // Brukernavn-regel (2–7 tegn, a–z/0–9)
    if (!$err && !preg_match('/^[a-z0-9]{2,7}$/', $bn)) {
        $err = "Brukernavn må være 2–7 tegn og bestå av a–z og/eller 0–9.";
    }

    // Finnes klassekoden?
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

    // Er brukernavnet ledig?
    if (!$err) {
        $chk = $conn->prepare("SELECT 1 FROM student WHERE brukernavn = ?");
        $chk->bind_param('s', $bn);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            $err = "Brukernavnet «$bn» er allerede i bruk.";
        }
        $chk->close();
    }

    // Sett inn hvis alt ok
    if (!$err) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO student (brukernavn, fornavn, etternavn, klassekode)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('ssss', $bn, $fn, $en, $kk);
            $stmt->execute();
            $stmt->close();

            // Succes-melding + nullstill felter
            $msg = "Student «$bn» ble lagt til i klasse «$kk».";
            $bn = $fn = $en = $kk = '';

        } catch (mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                $err = "Brukernavnet «$bn» finnes allerede.";
            } else {
                $err = "Databasefeil ved lagring: " . $e->getMessage();
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

    <form method="post" action="student_add.php" autocomplete="off">
        <label for="bn">Brukernavn <small>(2–7 tegn, a–z/0–9)</small></label>
        <input id="bn" name="brukernavn" type="text"
               value="<?= htmlspecialchars($bn) ?>" minlength="2" maxlength="7"
               pattern="[a-z0-9]{2,7}" required>

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
