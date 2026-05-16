<?php
// ═══════════════════════════════════════════════════════════
// Page 2 — Inscription d'un membre à un concours
// ═══════════════════════════════════════════════════════════
 
$xmlFile = __DIR__ . '/../club.xml';
$xml     = simplexml_load_file($xmlFile);
$message = '';
$error   = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membreRef  = $_POST['membreRef']  ?? '';
    $concoursId = $_POST['concoursId'] ?? '';
    $complexite = intval($_POST['complexite'] ?? 0);
    $temps      = intval($_POST['tempsExecution'] ?? 0);
 
    // Vérifications de base
    if (!$membreRef || !$concoursId || $complexite < 0 || $complexite > 100 || $temps <= 0) {
        $error = 'Veuillez remplir tous les champs correctement.';
    } else {
        // Vérifier que le membre n'est pas déjà inscrit
        $dejaInscrit = $xml->xpath(
            "//concours[@id='$concoursId']//participant[@membreRef='$membreRef']"
        );
 
        if ($dejaInscrit) {
            $error = 'Ce membre est déjà inscrit à ce concours !';
        } else {
            // Ajouter le participant via SimpleXML
            $concours = $xml->xpath("//concours[@id='$concoursId']")[0];
            $newPart  = $concours->participants->addChild('participant');
            $newPart->addAttribute('membreRef', $membreRef);
            $newPart->addChild('complexite', $complexite);
            $newPart->addChild('tempsExecution', $temps);
 
            // Sauvegarder le fichier XML
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            $dom->save($xmlFile);
 
            $message = "✅ Inscription réussie ! Le membre $membreRef a été ajouté au concours $concoursId.";
            $xml = simplexml_load_file($xmlFile); // Recharger
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Club Info_Tech — Inscription</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
 
<nav>
  <span class="logo">⚡ Club Info_Tech</span>
  <a href="index.php">Concours</a>
  <a href="inscription.php" class="active">Inscription</a>
  <a href="resultats.php">Résultats</a>
  <a href="requetes.php">XQuery</a>
</nav>
 
<div class="container">
 
  <div class="page-header">
    <h1>Inscription à un Concours</h1>
    <p>Inscrire un membre existant à un concours de sa catégorie</p>
  </div>
 
  <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
 
  <div class="card" style="max-width:560px;">
    <form method="POST">
 
      <div class="form-group">
        <label>Membre</label>
        <select name="membreRef" required>
          <option value="">— Sélectionner un membre —</option>
          <?php foreach ($xml->membres->membre as $m): ?>
            <option value="<?= $m['id'] ?>">
              <?= $m['id'] ?> — <?= $m->prenom ?> <?= $m->nom ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
 
      <div class="form-group">
        <label>Concours</label>
        <select name="concoursId" required>
          <option value="">— Sélectionner un concours —</option>
          <?php foreach ($xml->concours->concours as $c): ?>
            <option value="<?= $c['id'] ?>">
              <?= $c['id'] ?> — <?= $c->titre ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
 
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
          <label>Complexité (0–100)</label>
          <input type="number" name="complexite" min="0" max="100" placeholder="ex: 85" required>
        </div>
        <div class="form-group">
          <label>Temps d'exécution (ms)</label>
          <input type="number" name="tempsExecution" min="1" placeholder="ex: 120" required>
        </div>
      </div>
 
      <button type="submit" class="btn">Inscrire le membre</button>
    </form>
  </div>
 
</div>
</body>
</html>