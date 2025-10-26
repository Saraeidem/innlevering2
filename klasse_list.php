<?php
require 'db.php';

// Hent alle klasser
$res = $conn->query("SELECT klassekode, klassenavn, studiumkode FROM klasse ORDER BY klassekode");
?>
<!doctype html>
<html lang="no">
<head>
  <meta charset="utf-8">
  <title>Klasser</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Klasser</h2>
  <nav>
    <a href="index.php">Hjem</a>
    <a href="klasse_add.php">Legg til klasse</a>
  </nav>

  <table>
    <tr><th>Klassekode</th><th>Klassenavn</th><th>Studiumkode</th><th>Handling</th></tr>
    <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['klassekode']) ?></td>
        <td><?= htmlspecialchars($row['klassenavn']) ?></td>
        <td><?= htmlspecialchars($row['studiumkode']) ?></td>
        <td>
          <a href="klasse_delete.php?klassekode=<?= urlencode($row['klassekode']) ?>"
             onclick="return confirm('Slette klasse <?= htmlspecialchars($row['klassekode']) ?>? NB: går ikke hvis studenter finnes');">
             Slett
          </a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
