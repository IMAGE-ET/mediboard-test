<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */

global $can, $m, $g;
CAppUI::requireModuleFile("bloodSalvage", "inc_personnel");


$blood_salvage      = new CBloodSalvage();
$date               = mbGetValueFromGetOrSession("date", mbDate());
$rspo               = mbGetValueFromGetOrSession("rspo");
$totaltime          = "00:00:00";
$modif_operation    = $date>=mbDate();
$timing             = array();
$tabAffected        = array();
/*
 * Liste des cell saver : Figée pour le moment à ces trois valeurs. 
 */
$list_cell_saver = array("1"=> "DIDÉCO ÉLECTA N°1 série B 013663 C 03", 
													"2"=>"DIDÉCO ÉLECTA N°2série B 012740 C 02", 
													"3"=> "FRÉSÉNIUS CATS N° série 1CAA1090");

$list_nurse_sspi= CPersonnel::loadListPers("reveil");

/*
 * Liste d'incidents transfusionnels possibles.
 */
$liste_incident= array("Frisson",
                      "Hyperthermie",
                      "Fuite sur consommable",
                      "Déclaration correspondant hémovigilance fait", 
                      "Déclaration technicien machine fait");

$tabAffected = array();
$timingAffect = array();

if($rspo) {
	$blood_salvage->load($rspo);
	$blood_salvage->loadRefsFwd();
	$blood_salvage->_ref_operation->loadRefPatient();
	
  $timing["_recuperation_start"]       = array();
  $timing["_recuperation_end"]         = array();
  $timing["_transfusion_start"]        = array();
  $timing["_transfusion_end"]          = array();
  foreach($timing as $key => $value) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $blood_salvage->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $blood_salvage->$key);
    }
  }
  
  loadAffected($blood_salvage->_id, $list_nurse_sspi, $tabAffected, $timingAffect);
  
}

$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("blood_salvage",$blood_salvage);
$smarty->assign("totaltime", $totaltime);
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("timing", $timing);
$smarty->assign("timingAffect", $timingAffect);
$smarty->assign("tabAffected",$tabAffected);
$smarty->assign("list_cell_saver", $list_cell_saver);
$smarty->assign("list_nurse_sspi", $list_nurse_sspi);
$smarty->assign("liste_incident", $liste_incident);


$smarty->display("vw_bloodSalvage_sspi.tpl");

?>