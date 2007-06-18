<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
require_once($AppUI->getModuleFile($m, "inc_vw_affectations"));

$date = mbGetValueFromGetOrSession("date", mbDate()); 
$pathos = new CDiscipline();
$heureLimit = "16:00:00";

// Liste des patients à placer
$groupSejourNonAffectes = array();

if ($can->edit) {
  // Admissions de la veille
  $dayBefore = mbDate("-1 days", $date);
  $where = array(
    "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
    "type" => "!= 'exte'",
    "annule" => "= '0'"
  );

  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where);
  
  // Admissions du matin
  $where = array(
    "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
    "type" => "!= 'exte'",
    "annule" => "= '0'"
  );
  
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where);
  
  // Admissions du soir
  $where = array(
    "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
    "type" => "!= 'exte'",
    "annule" => "= '0'"
  );
  
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where);
  
  // Admissions antérieures
  $twoDaysBefore = mbDate("-2 days", $date);
  $where = array(
    "annule" => "= '0'",
    "'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue"
  );
  
  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where);
}



// Création du template
$smarty = new CSmartyDP();
$smarty->assign("pathos",$pathos);
$smarty->assign("date" , $date);
$smarty->assign("heureLimit", $heureLimit);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);
$smarty->display("vw_idx_pathologies.tpl");

?>