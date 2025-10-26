<?php
require 'db.php';
$sql = "SELECT s.brukernavn, s.fornavn, s.etternavn, s.klassekode, k.klassenavn
        FROM student s
        LEFT JOIN klasse k ON s.klassekode = k.klassekode
        ORDER BY s.brukernavn";
$res = $conn->query($sql);
?>
<!doctype html>
<html lang="no">
<head>
  <meta charset="utf-8">
  <title>Studenter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Studenter</h2>
  <nav>
    <a href="index.php">Hjem</a>
    <a href="student_add.php">Legg til student</a>
  </nav>

  <table>
    <tr><th>Brukernavn</th><th>Fornavn</th><th>Etternavn</th><th>Klasse</th><th>Handling</th></tr>
    <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['brukernavn']) ?></td>
        <td><?= htmlspecialchars($row['fornavn']) ?></td>
        <td><?= htmlspecialchars($row['etternavn']) ?></td>
        <td><?= htmlspecialchars($row['klassekode']) ?> – <?= htmlspecialchars($row['klassenavn'] ?? '') ?></td>
        <td>
          <a href="student_delete.php?brukernavn=<?= urlencode($row['brukernavn']) ?>"
             onclick="return confirm('Slette student <?= htmlspecialchars($row['brukernavn']) ?>?');">
             Slett
          </a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
