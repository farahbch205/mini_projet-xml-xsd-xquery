<?php
// ═══════════════════════════════════════════════════════════
// Page 1 — Liste des concours
// Lecture de club.xml avec SimpleXML
// ═══════════════════════════════════════════════════════════
 
$xmlFile = __DIR__ . '/../club.xml';
$xml = simplexml_load_file($xmlFile);
 
// Fonction pour obtenir le badge CSS selon la catégorie
function badgeClass($libelle) {
    if (str_contains($libelle, 'Artificielle')) return 'badge-ia';
    if (str_contains($libelle, 'Web'))          return 'badge-web';
    return 'badge-sec';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Club Info_Tech — Concours</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
 
<nav>
  <span class="logo">⚡ Club Info_Tech</span>
  <a href="index.php" class="active">Concours</a>
  <a href="inscription.php">Inscription</a>
  <a href="resultats.php">Résultats</a>
  <a href="requetes.php">XQuery</a>
</nav>
 
<div class="container">
 
  <div class="page-header">
    <h1>Liste des Concours</h1>
    <p>Tous les concours organisés par le club, triés par date</p>
  </div>
 
  <div class="grid">
    <?php
    // Trier les concours par date
    $concours = [];
    foreach ($xml->concours->concours as $c) {
        $catId   = (string) $c['categorieRef'];
        $cat     = $xml->xpath("//categorie[@id='$catId']")[0];
        $libelle = (string) $cat['libelle'];
 
        $concours[] = [
            'id'          => (string) $c['id'],
            'titre'       => (string) $c->titre,
            'date'        => (string) $c['date'],
            'coefficient' => (string) $c['coefficient'],
            'libelle'     => $libelle,
            'nbPart'      => count($c->participants->participant),
        ];
    }
 
    // Tri par date croissante
    usort($concours, fn($a, $b) => strcmp($a['date'], $b['date']));
 
    foreach ($concours as $c):
        $badge = badgeClass($c['libelle']);
    ?>
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:1rem;">
        <span class="badge <?= $badge ?>"><?= htmlspecialchars($c['libelle']) ?></span>
        <span style="font-family:'Space Mono',monospace; font-size:0.75rem; color:var(--muted);">
          <?= $c['id'] ?>
        </span>
      </div>
 
      <h3 style="font-size:1rem; margin-bottom:0.8rem;">
        <?= htmlspecialchars($c['titre']) ?>
      </h3>
 
      <div style="display:flex; gap:1.5rem; font-size:0.85rem; color:var(--muted);">
        <span>📅 <?= $c['date'] ?></span>
        <span>×<?= $c['coefficient'] ?> coeff</span>
        <span>👥 <?= $c['nbPart'] ?> participants</span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
 
</div>
</body>
</html>