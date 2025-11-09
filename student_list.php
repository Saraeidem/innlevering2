<?php
require __DIR__ . '/db.php';

// Les eventuelle meldinger fra redirect
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$err = isset($_GET['err']) ? $_GET['err'] : null;

// Hent studenter + klassenavn
$studenter = [];
try {
    $sql = "
        SELECT s.brukernavn, s.fornavn, s.etternavn, s.klassekode, k.klassenavn
        FROM student s
        LEFT JOIN klasse k ON k.klassekode = s.klassekode
        ORDER BY s.klassekode, s.brukernavn
    ";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $studenter[] = $row;
    }
} catch (mysqli_sql_exception $e) {
    $err = "Klarte ikke å hente studenter: " . $e->getMessage();
}
?>
<!doctype html>
<html lang="no">
<head>
    <meta charset="utf-8">
    <title>Studenter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:960px;margin:2rem auto;padding:0 1rem;line-height:1.45}
        nav a{margin-right:1rem;color:#0b63c4;text-decoration:none}
        .notice,.error{padding:.75rem 1rem;border-radius:.5rem;margin:1rem 0}
        .notice{background:#e8f6ff;border:1px solid #b9e4ff}
        .error{background:#ffecec;border:1px solid #ffb8b8}
        table{border-collapse:collapse;width:100%;margin-top:1rem}
        th,td{border:1px solid #ddd;padding:.55rem .6rem;text-align:left}
        form{display:inline}
        button{padding:.35rem .6rem;border:1px solid #cc0000;background:#fff;border-radius:.4rem;cursor:pointer}
        button:hover{background:#fff5f5}
    </style>
</head>
<body>
<h2>Studenter</h2>
<nav>
    <a href="index.php">Hjem</a>
    <a href="student_add.php">Legg til student</a>
</nav>

<?php if ($msg): ?><div class="notice"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<?php if (!$studenter): ?>
    <p>Ingen studenter registrert ennå.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Brukernavn</th>
            <th>Fornavn</th>
            <th>Etternavn</th>
            <th>Klasse</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($studenter as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['brukernavn']) ?></td>
            <td><?= htmlspecialchars($s['fornavn']) ?></td>
            <td><?= htmlspecialchars($s['etternavn']) ?></td>
            <td>
                <?= htmlspecialchars($s['klassekode']) ?>
                <?= $s['klassenavn'] ? ' – ' . htmlspecialchars($s['klassenavn']) : '' ?>
            </td>
            <td>
                <form method="post" action="student_delete.php" onsubmit="return confirm('Slette <?= htmlspecialchars($s['brukernavn']) ?>?')">
                    <input type="hidden" name="brukernavn" value="<?= htmlspecialchars($s['brukernavn']) ?>">
                    <button type="submit">Slett</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</body>
</html>
