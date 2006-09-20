<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$clefs    = mbGetValueFromGetOrSession("clefs");
$code     = mbGetValueFromGetOrSession("code");
$selacces = mbGetValueFromGetOrSession("selacces");
$seltopo1 = mbGetValueFromGetOrSession("seltopo1");
$seltopo2 = mbGetValueFromGetOrSession("seltopo2");

// Connexion a la base de donnees pour la recherche
do_connect($AppUI->cfg["baseCCAM"]);

// Création de la requête
$query = "SELECT CODE, LIBELLELONG FROM actes WHERE 0";

// Si un autre élément est rempli
if ($code || $clefs || $selacces || $seltopo1) {
  $query .= " or (1";
  // On fait la recherche sur le code
  if ($code != "") {
	$query .= " AND CODE LIKE '" . addslashes($code) . "%'";
  }
  // On explode les mots clefs
  if ($clefs != "") {
    $listeClefs = explode(" ", $clefs);
    foreach ($listeClefs as $key => $value)
      $query .= " AND (LIBELLELONG LIKE '%" .  addslashes($value) . "%')";
  }
  
  // On trie selon les voies d'accès
  if ($selacces)
    $query .= " AND CODE LIKE '___" . $selacces . "___'";
  // On tris selon les topologies de niveau 1 ou 2
  if ($seltopo1) {
    if ($seltopo2)
      $query .= " AND CODE LIKE '" . $seltopo2 . "_____'";
    else
      $query .= " AND CODE LIKE '" . $seltopo1 . "______'";
  }
  
  $query .= ")";
}

$query .= " ORDER BY CODE LIMIT 0 , 100";

//Codes correspondants à la requete
$result = db_exec($query, $AppUI->cfg["baseCCAM"]);
$i = 0;
$codes = array();
while($row = db_fetch_array($result)) {
  $codes[$i]["code"] = $row["CODE"];
  $codes[$i]["texte"] = $row["LIBELLELONG"];
  $i++;
}
$numcodes = $i;

//On récupère les voies d'accès
$query = "select * from acces1";
$result = db_exec($query, $AppUI->cfg["baseCCAM"]);
$i = 1;
while($row = db_fetch_array($result)) {
  $acces[$i]["code"] = $row["CODE"];
  $acces[$i]["texte"] = $row["ACCES"];
  $i++;
}

//On récupère les appareils : topographie1
$query = "select * from topographie1";
$result = db_exec($query, $AppUI->cfg["baseCCAM"]);

$i = 1;
while($row = db_fetch_array($result)) {
  $topo1[$i]["code"] = $row["CODE"];
  $topo1[$i]["texte"] = $row["LIBELLE"];
  $i++;
}

// On récupère les systèmes correspondants à l'appareil : topographie2
$query = "SELECT * FROM topographie2 WHERE PERE = '$seltopo1'";
$result = db_exec($query, $AppUI->cfg["baseCCAM"]);
$topo2 = array();
$i = 1;
while($row = db_fetch_array($result)) {
  $topo2[$i]["code"] = $row["CODE"];
  $topo2[$i]["texte"] = $row["LIBELLE"];
  $i++;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("clefs"   , $clefs);
$smarty->assign("selacces", $selacces);
$smarty->assign("seltopo1", $seltopo1);
$smarty->assign("seltopo2", $seltopo2);
$smarty->assign("code"    , $code);
$smarty->assign("acces"   , $acces);
$smarty->assign("topo1"   , $topo1);
$smarty->assign("topo2"   , $topo2);
$smarty->assign("codes"   , $codes);
$smarty->assign("numcodes", $numcodes);

$smarty->display("vw_find_code.tpl");

?>