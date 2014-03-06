<?php 

/**
 * $Id$
 *  
 * @category Urgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCando::checkRead();

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);

$sejour = $consult->loadRefSejour();

$rpu = $sejour->loadRefRPU();

$services = array();

if ($rpu && $rpu->_id) {
  // Mise en session du rpu_id
  $_SESSION["dPurgences"]["rpu_id"] = $rpu->_id;
  $rpu->loadRefSejourMutation();
  $affectation = $sejour->loadRefCurrAffectation();

  $affectation->loadRefService();
  // Urgences pour un séjour "urg"
  if ($sejour->type == "urg") {
    $services = CService::loadServicesUrgence();
  }

  // UHCD pour un séjour "comp" et en UHCD
  if ($sejour->type == "comp" && $sejour->UHCD) {
    $services = CService::loadServicesUHCD();
  }

  if ($affectation->_ref_service && $affectation->_ref_service->radiologie == "1") {
    $services = CService::loadServicesImagerie();
  }

  if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
    $mode_sortie = new CModeSortieSejour();
    $where = array(
      "actif" => "= '1'",
    );
    $list_mode_sortie = $mode_sortie->loadGroupList($where);
  }
}

$where = array();
$where["entree"] = "<= '".CMbDT::dateTime()."'";
$where["sortie"] = ">= '".CMbDT::dateTime()."'";
$where["function_id"] = "IS NOT NULL";

$affectation = new CAffectation();
/** @var CAffectation[] $blocages_lit */
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";

foreach ($blocages_lit as $blocage) {
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";

  if ($affectation->loadObject($where)) {
    $sejour = $affectation->loadRefSejour();
    $patient = $sejour->loadRefPatient();
    $blocage->_ref_lit->_view .= " indisponible jusqu'à ".CMbDT::transform($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y");
    $blocage->_ref_lit->_view .= " (".$patient->_view." (".strtoupper($patient->sexe).") ";
    $blocage->_ref_lit->_view .= CAppUI::conf("dPurgences age_patient_rpu_view") ? $patient->_age.")" : ")" ;
  }
}

// Tableau de contraintes pour les champs du RPU
// Contraintes sur le mode d'entree / provenance
//$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Contraintes sur le mode de sortie / destination
$contrainteDestination["mutation" ] = array("", 1, 2, 3, 4);
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"   ] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["mutation" ] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"   ] = array("", "FUGUE", "SCAM", "PSA", "REO");

$smarty = new CSmartyDP();

$smarty->assign("consult"       , $consult);
$smarty->assign("rpu"           , $rpu);
$smarty->assign("sejour"        , $sejour);
$smarty->assign("services"      , $services);
$smarty->assign("now"           , CMbDT::dateTime());
$smarty->assign("blocages_lit"  , $blocages_lit);
$smarty->assign("consult_anesth", null);
$smarty->assign("contrainteProvenance" , $contrainteProvenance );
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);

$smarty->display("inc_vw_rpu.tpl");