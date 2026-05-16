(: ════════════════════════════════════
   Q1 — Liste complète des membres
   ════════════════════════════════════ :)
let $doc := doc("club.xml")
return
<resultats>

<Q1>
<membres>{
  for $m in $doc//membre
  let $cat := $doc//categorie[@id = $m/@categorieRef]
  return
    <membre id="{$m/@id}">
      <nomComplet>{$m/prenom/text()} {$m/nom/text()}</nomComplet>
      <email>{$m/email/text()}</email>
      <categorie>{$cat/@libelle/string()}</categorie>
    </membre>
}</membres>
</Q1>

<Q2>
<concours>{
  for $c in $doc//concours/concours
  let $cat := $doc//categorie[@id = $c/@categorieRef]
  order by xs:date($c/@date) ascending
  return
    <concours id="{$c/@id}">
      <titre>{$c/titre/text()}</titre>
      <date>{$c/@date/string()}</date>
      <coefficient>{$c/@coefficient/string()}</coefficient>
      <categorie>{$cat/@libelle/string()}</categorie>
    </concours>
}</concours>
</Q2>

<Q3>
<resultats>{
  for $c in $doc//concours/concours
  return
    <concours titre="{$c/titre/text()}">{
      for $p in $c//participant
      let $m     := $doc//membre[@id = $p/@membreRef]
      let $score := (xs:decimal($p/complexite) + xs:decimal($p/tempsExecution))
                    * xs:decimal($c/@coefficient)
      return
        <participant>
          <nom>{$m/prenom/text()} {$m/nom/text()}</nom>
          <complexite>{$p/complexite/text()}</complexite>
          <tempsExecution>{$p/tempsExecution/text()}</tempsExecution>
          <score>{format-number($score, "0.00")}</score>
        </participant>
    }</concours>
}</resultats>
</Q3>

<Q4>
<vainqueurs>{
  for $c in $doc//concours/concours
  let $scores :=
    for $p in $c//participant
    return (xs:decimal($p/complexite) + xs:decimal($p/tempsExecution))
           * xs:decimal($c/@coefficient)
  let $scoreMax := max($scores)
  return
    <concours titre="{$c/titre/text()}">{
      for $p in $c//participant
      let $m     := $doc//membre[@id = $p/@membreRef]
      let $score := (xs:decimal($p/complexite) + xs:decimal($p/tempsExecution))
                    * xs:decimal($c/@coefficient)
      where $score = $scoreMax
      return
        <vainqueur score="{format-number($score, '0.00')}">
          <nom>{$m/nom/text()}</nom>
          <prenom>{$m/prenom/text()}</prenom>
        </vainqueur>
    }</concours>
}</vainqueurs>
</Q4>

<Q5>
<membres categorie="Intelligence Artificielle">{
  let $catId := $doc//categorie[@libelle = "Intelligence Artificielle"]/@id
  for $m in $doc//membre[@categorieRef = $catId]
  order by $m/nom/text() ascending,
           $m/prenom/text() ascending
  return
    <membre id="{$m/@id}">
      <nom>{$m/nom/text()}</nom>
      <prenom>{$m/prenom/text()}</prenom>
      <email>{$m/email/text()}</email>
    </membre>
}</membres>
</Q5>

</resultats>