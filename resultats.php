<?php
// ═══════════════════════════════════════════════════════════
// Page 3 — Résultats d'un concours + vainqueur
// ═══════════════════════════════════════════════════════════
 
$xmlFile     = __DIR__ . '/../club.xml';
$xml         = simplexml_load_file($xmlFile);
$concoursId  = $_GET['concours'] ?? '';
$concoursData = null;
 
if ($concoursId) {
    $cList = $xml->xpath("//concours[@id='$concoursId']");
    if ($cList) {
        $c           = $cList[0];
        $coefficient = (float) $c['coefficient'];
        $participants = [];
        $maxScore    = 0;
 
        foreach ($c->participants->participant as $p) {
            $membreRef = (string) $p['membreRef'];
            $mList     = $xml->xpath("//membre[@id='$membreRef']");
            $m         = $mList ? $mList[0] : null;
            $score     = round(
                (intval($p->complexite) + intval($p->tempsExecution)) * $coefficient,
                2
            );
            if ($score > $maxScore) $maxScore = $score;
 
            $participants[] = [
                'ref'        => $membreRef,
                'nom'        => $m ? "$m->prenom $m->nom" : $membreRef,
                'complexite' => (int) $p->complexite,
                'temps'      => (int) $p->tempsExecution,
                'score'      => $score,
            ];
        }
 
        // Trier par score décroissant
        usort($participants, fn($a, $b) => $b['score'] <=> $a['score']);
 
        $concoursData = [
            'titre'       => (string) $c->titre,
            'date'        => (string) $c['date'],
            'coefficient' => $coefficient,
            'participants' => $participants,
            'maxScore'    => $maxScore,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Club Info_Tech — Résultats</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
 
<nav>
  <span class="logo">⚡ Club Info_Tech</span>
  <a href="index.php">Concours</a>
  <a href="inscription.php">Inscription</a>
  <a href="resultats.php" class="active">Résultats</a>
  <a href="requetes.php">XQuery</a>
</nav>
 
<div class="container">
 
  <div class="page-header">
    <h1>Résultats des Concours</h1>
    <p>Scores et vainqueurs par concours</p>
  </div>
 
  <!-- Sélecteur de concours -->
  <form method="GET" style="margin-bottom:2rem; display:flex; gap:1rem; align-items:center;">
    <select name="concours" style="max-width:400px;">
      <option value="">— Choisir un concours —</option>
      <?php foreach ($xml->concours->concours as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $concoursId === (string)$c['id'] ? 'selected' : '' ?>>
          <?= $c['id'] ?> — <?= $c->titre ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn">Voir les résultats</button>
  </form>
 
  <?php if ($concoursData): ?>
 
    <!-- Vainqueur -->
    <?php
    $winners = array_filter(
        $concoursData['participants'],
        fn($p) => $p['score'] == $concoursData['maxScore']
    );
    foreach ($winners as $w):
    ?>
    <div class="winner-card" style="margin-bottom:2rem;">
      <span class="winner-icon">🏆</span>
      <div>
        <div style="font-size:0.75rem; color:var(--accent3); font-weight:700; margin-bottom:0.2rem;">
          VAINQUEUR
        </div>
        <div style="font-weight:700; font-size:1.1rem;"><?= htmlspecialchars($w['nom']) ?></div>
      </div>
      <span class="winner-score"><?= $w['score'] ?> pts</span>
    </div>
    <?php endforeach; ?>
 
    <!-- Tableau des participants -->
    <div class="card" style="padding:0; overflow:hidden;">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Participant</th>
            <th>Complexité</th>
            <th>Temps (ms)</th>
            <th>Score</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($concoursData['participants'] as $i => $p): ?>
          <tr <?= $p['score'] == $concoursData['maxScore'] ? 'style="color:var(--accent3);"' : '' ?>>
            <td style="font-family:'Space Mono',monospace; color:var(--muted);">
              <?= $p['score'] == $concoursData['maxScore'] ? '🥇' : ($i + 1) ?>
            </td>
            <td style="font-weight:600;"><?= htmlspecialchars($p['nom']) ?></td>
            <td><?= $p['complexite'] ?></td>
            <td><?= $p['temps'] ?></td>
            <td style="font-family:'Space Mono',monospace; font-weight:700;">
              <?= $p['score'] ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
 
    <p style="color:var(--muted); font-size:0.85rem; margin-top:1rem;">
      Formule : score = (complexite + tempsExecution) × <?= $concoursData['coefficient'] ?>
    </p>
 
  <?php elseif ($concoursId): ?>
    <div class="alert alert-error">Concours introuvable.</div>
  <?php endif; ?>
 
</div>
</body>
</html>