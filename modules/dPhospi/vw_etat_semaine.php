<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Liste des chirurgiens
$listChirs = array();
$listPats = array();

// Récupération des admissions à affecter
function loadSejourNonAffectes($where) {
  global $listChirs, $listPats, $listFunctions, $g;
  
  $leftjoin = array(
    "affectation"     => "sejour.sejour_id = affectation.sejour_id"
  );
  $where["sejour.group_id"] = "= '$g'";
  $where[] = "affectation.affectation_id IS NULL";
  
  $sejourNonAffectes = new CSejour;
  $sejourNonAffectes = $sejourNonAffectes->loadList($where, null, null, null, $leftjoin);
  
  foreach ($sejourNonAffectes as $keySejour => $valSejour) {
    $sejour =& $sejourNonAffectes[$keySejour];
  }
  
  return $sejourNonAffectes;
}

$today = mbDate()." 01:00:00";
$to = mbDateTime("-1 second", $today);
$list = array();
for($i = 1; $i <= 7; $i++) {
  $from = mbDateTime("+1 second", $to);
  $to = mbDateTime("+1 day", $to);
  $where = array(
    "entree_prevue" => "BETWEEN '$from' AND '$to'",
    "type" => "!= 'exte'",
    "annule" => "= '0'"
  );
  $list[$from] = loadSejourNonAffectes($where);
}

// Création du template
$smarty = new CSmartyDP();


$smarty->assign("list" , $list);

$smarty->display("vw_etat_semaine.tpl");

?>