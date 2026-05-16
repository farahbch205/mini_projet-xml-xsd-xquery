<?php
$BASEX_URL  = 'http://localhost:8984/rest/club';
$BASEX_USER = 'admin';
$BASEX_PASS = 'admin';
 
$result = '';
$error  = '';
$query  = $_POST['query'] ?? '';
 
$exemples = [
    'Liste des membres'   => 'for $m in //membre return <membre>{$m/nom/text()} {$m/prenom/text()}</membre>',
    'Liste des concours'  => 'for $c in //concours[@id] order by $c/@date return <concours>{$c/titre/text()}</concours>',
    'Scores participants' => 'for $p in //participant let $s := xs:integer($p/complexite) + xs:integer($p/tempsExecution) return <score>{$s}</score>',
];
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $query) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $BASEX_URL . '?query=' . urlencode($query),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => "$BASEX_USER:$BASEX_PASS",
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);
 
    if ($curlErr) {
        $error = "Connexion impossible à BaseX.";
    } elseif ($httpCode >= 400) {
        $error = "Erreur BaseX ($httpCode)";
    } else {
        $result = $response;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Club Info_Tech — XQuery</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
 
<nav>
  <span class="logo">⚡ Club Info_Tech</span>
  <a href="index.php">Concours</a>
  <a href="inscription.php">Inscription</a>
  <a href="resultats.php">Résultats</a>
  <a href="requetes.php" class="active">XQuery</a>
</nav>
 
<div class="container">
 
  <div class="page-header">
    <h1>Requêtes XQuery libres</h1>
    <p>Envoyez n'importe quelle requête XQuery à BaseX</p>
  </div>
 
  <!-- Exemples sous forme de formulaires séparés -->
  <div style="margin-bottom:1.5rem;">
    <p class="section-title">Exemples rapides</p>
    <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
      <?php foreach ($exemples as $label => $xq): ?>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="query" value="<?= htmlspecialchars($xq) ?>">
          <button type="submit" class="btn"
            style="background:#f0f0ff; color:#4f46e5; border:1px solid #c7d2fe; font-size:0.85rem; padding:0.5rem 1.2rem;">
            <?= htmlspecialchars($label) ?>
          </button>
        </form>
      <?php endforeach; ?>
    </div>
  </div>
 
  <!-- Formulaire principal -->
  <div class="card">
    <form method="POST">
      <div class="form-group">
        <label>Requête XQuery</label>
        <textarea id="query" name="query" rows="6"
          placeholder="Entrez votre requête XQuery ici..."
          style="font-family:monospace; font-size:0.9rem;"><?= htmlspecialchars($query) ?></textarea>
      </div>
      <button type="submit" class="btn">▶ Exécuter</button>
    </form>
  </div>
 
  <?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
  <?php endif; ?>
 
  <?php if ($result): ?>
    <p class="section-title">Résultat</p>
    <div class="code-output"><?= htmlspecialchars($result) ?></div>
  <?php endif; ?>
 
</div>
</body>
</html>