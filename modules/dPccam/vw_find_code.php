<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$ds = CSQLDataSource::get("ccamV2");

$object_class    = CValue::getOrSession("object_class");
$clefs           = CValue::getOrSession("clefs");
$code            = CValue::getOrSession("code");
$selacces        = CValue::getOrSession("selacces");
$seltopo1        = CValue::getOrSession("seltopo1");
$seltopo2        = CValue::getOrSession("seltopo2");

$chap1old        = CValue::session("chap1");
$chap1           = CValue::getOrSession("chap1");
if($chap1 && $chap1 == $chap1old) {
  $chap2old = CValue::session("chap2");
  $chap2    = CValue::getOrSession("chap2");
} else {
  $chap2 = "";
  CValue::setSession("chap2");
}
if($chap2 && $chap2 == $chap2old) {
  $chap3old = CValue::session("chap3");
  $chap3    = CValue::getOrSession("chap3");
} else {
  $chap3 = "";
  CValue::setSession("chap3");
}
if($chap3 && $chap3 == $chap3old) {
  $chap4old = CValue::session("chap4");
  $chap4    = CValue::getOrSession("chap4");
} else {
  $chap4 = "";
  CValue::setSession("chap4");
}

// On récupère les voies d'accès
$query = "SELECT * FROM acces1";
$result = $ds->exec($query);
$i = 1;
while($row = $ds->fetchArray($result)) {
  $acces[$i]["code"]  = $row["CODE"];
  $acces[$i]["texte"] = $row["ACCES"];
  $i++;
}

// On récupère les appareils : topographie1
$query = "SELECT * FROM topographie1";
$result = $ds->exec($query);

$i = 1;
while($row = $ds->fetchArray($result)) {
  $topo1[$i]["code"]  = $row["CODE"];
  $topo1[$i]["texte"] = $row["LIBELLE"];
  $i++;
}

// On récupère les systèmes correspondants à l'appareil : topographie2
$query = "SELECT * FROM topographie2 WHERE PERE = '$seltopo1'";
$result = $ds->exec($query);
$topo2 = array();
$i = 1;
while($row = $ds->fetchArray($result)) {
  $topo2[$i]["code"]  = $row["CODE"];
  $topo2[$i]["texte"] = $row["LIBELLE"];
  $i++;
}

// On récupère les chapitres de niveau 1
$listChap1 = array();
$query = "SELECT * FROM arborescence WHERE CODEPERE = '000001' ORDER BY RANG";
$result = $ds->exec($query);
while($row = $ds->fetchArray($result)) {
  $codeChap = $row["CODEMENU"];
  $listChap1[$codeChap]["rang"]  = substr($row["RANG"], 4, 2);
  $listChap1[$codeChap]["texte"] = $row["LIBELLE"];
}

// On récupère les chapitres de niveau 2
$listChap2 = array();
if($chap1) {
  $query = "SELECT * FROM arborescence WHERE CODEPERE = '$chap1' ORDER BY RANG";
  $result = $ds->exec($query);
  while($row = $ds->fetchArray($result)) {
    $codeChap = $row["CODEMENU"];
    $listChap2[$codeChap]["rang"]  = substr($row["RANG"], 4, 2);
    $listChap2[$codeChap]["texte"] = $row["LIBELLE"];
  }
}

// On récupère les chapitres de niveau 3
$listChap3 = array();
if($chap2) {
  $query = "SELECT * FROM arborescence WHERE CODEPERE = '$chap2' ORDER BY RANG";
  $result = $ds->exec($query);
  while($row = $ds->fetchArray($result)) {
    $codeChap = $row["CODEMENU"];
    $listChap3[$codeChap]["rang"]  = substr($row["RANG"], 4, 2);
    $listChap3[$codeChap]["texte"] = $row["LIBELLE"];
  }
}

// On récupère les chapitres de niveau 4
$listChap4 = array();
if($chap3) {
  $query = "SELECT * FROM arborescence WHERE CODEPERE = '$chap3' ORDER BY RANG";
  $result = $ds->exec($query);
  while($row = $ds->fetchArray($result)) {
    $codeChap = $row["CODEMENU"];
    $listChap4[$codeChap]["rang"]  = substr($row["RANG"], 4, 2);
    $listChap4[$codeChap]["texte"] = $row["LIBELLE"];
  }
}

// Création de la requête
$query = "SELECT CODE, LIBELLELONG FROM actes WHERE 0";

// Si un autre élément est rempli
if ($code || $clefs || $selacces || $seltopo1 || $chap1 || $chap2 || $chap3 || $chap4) {
  $query .= " or (DATEFIN = '00000000'";
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
  
  // On filtre selon les voies d'accès
  if ($selacces)
    $query .= " AND CODE LIKE '___" . $selacces . "___'";

  // On filtre selon les topologies de niveau 1 ou 2
  if ($seltopo1) {
    if ($seltopo2)
      $query .= " AND CODE LIKE '" . $seltopo2 . "_____'";
    else
      $query .= " AND CODE LIKE '" . $seltopo1 . "______'";
  }
  
  // On filtre selon le chapitre 4
  if($chap4) {
    $query .= " AND ARBORESCENCE4 = '0000".$listChap4[$chap4]["rang"]."'";
  }
  // On filtre selon le chapitre 3
  if($chap3) {
    $query .= " AND ARBORESCENCE3 = '0000".$listChap3[$chap3]["rang"]."'";
  }
  // On filtre selon le chapitre 2
  if($chap2) {
    $query .= " AND ARBORESCENCE2 = '0000".$listChap2[$chap2]["rang"]."'";
  }
  // On filtre selon le chapitre 1
  if($chap1) {
    $query .= " AND ARBORESCENCE1 = '0000".$listChap1[$chap1]["rang"]."'";
  }
  
  $query .= ")";
}

$query .= " ORDER BY CODE LIMIT 0 , 100";

//Codes correspondants à la requete
$result = $ds->exec($query);
$i = 0;
$codes = array();
while($row = $ds->fetchArray($result)) {
  $codes[$i]["code"]  = $row["CODE"];
  $codes[$i]["texte"] = $row["LIBELLELONG"];
  $i++;
}
$numcodes = $i;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_class", $object_class);
$smarty->assign("clefs"       , $clefs);
$smarty->assign("selacces"    , $selacces);
$smarty->assign("seltopo1"    , $seltopo1);
$smarty->assign("seltopo2"    , $seltopo2);
$smarty->assign("chap1"       , $chap1);
$smarty->assign("chap2"       , $chap2);
$smarty->assign("chap3"       , $chap3);
$smarty->assign("chap4"       , $chap4);
$smarty->assign("code"        , $code);
$smarty->assign("acces"       , $acces);
$smarty->assign("topo1"       , $topo1);
$smarty->assign("topo2"       , $topo2);
$smarty->assign("listChap1"   , $listChap1);
$smarty->assign("listChap2"   , $listChap2);
$smarty->assign("listChap3"   , $listChap3);
$smarty->assign("listChap4"   , $listChap4);
$smarty->assign("codes"       , $codes);
$smarty->assign("numcodes"    , $numcodes);

$smarty->display("vw_find_code.tpl");

?>