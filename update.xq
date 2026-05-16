(: ════════════════════════════════════════════
   updates.xq — XQuery Update Facility
   Club Info_Tech — Université de Skikda
   ════════════════════════════════════════════ :)

(: ── UPDATE 1 : INSERT ── :)
(: Ajout d'un nouveau membre M011 dans la catégorie C2 :)
insert node
  <membre id="M011" categorieRef="C2">
    <nom>Zerrouki</nom>
    <prenom>Nassim</prenom>
    <email>n.zerrouki@club.dz</email>
  </membre>
into //membres

,

(: ── UPDATE 2 : MODIFICATION ── :)
(: Modification du coefficient du concours CO2 : 1.2 → 2.0 :)
replace value of node
  //concours[@id="CO2"]/@coefficient
with "2.0"

,

(: ── UPDATE 3 : SUPPRESSION ── :)
(: Suppression du participant M002 du concours CO1 :)
(: Le concours CO1 subsiste avec ses autres participants :)
delete node
  //concours[@id="CO1"]
    //participant[@membreRef="M002"]