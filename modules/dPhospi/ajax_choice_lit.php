<?php /* $Id:ajax_choice_lit.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 11749 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Récupération des  paramètres
$chambre_id = CValue::get("chambre_id");
$patient_id = CValue::get("patient_id");
$vue_hospi  = CValue::get("vue_hospi", false);
$date       = CValue::getOrSession("date", mbDate());

$chambre = new CChambre();
$chambre->load($chambre_id);
$chambre->loadRefsLits();

$patient = new CPatient();
$patient->load($patient_id);

$affectations = array();

$nb_lits=0;
$q = "";
foreach($chambre->_ref_lits as $lit){
  if($nb_lits){
    $q .= " OR ";
  }
  if($vue_hospi){
  	$q .= "lit_id = '".$lit->_id."'";
  }
  else{  $q .= "rpu.box_id = '".$lit->_id."'"; }
  $nb_lits++;
}
//Si on se trouve dans le module hospi
if($vue_hospi){		
	$date_min = mbDateTime($date);
	$date_max = mbDateTime("+1 day", $date_min);

	$affectation = new CAffectation();
	$where["entree"] = "<= '$date_max'";
	$where["sortie"] = ">= '$date_min'";
	$where[] = $q;

  $affs = $affectation->loadList($where);
	foreach($affs as $_aff) {
	 $affectations[$_aff->lit_id] = "1";
	}	
}
//Si on vient du module urgences
else{
	$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
	$date_before    = mbDate("-$date_tolerance DAY", $date);
	$date_after     = mbDate("+1 DAY", $date);

	$ljoin = array();
	$ljoin["sejour"] = "rpu.sejour_id = sejour.sejour_id";
	$where = array();
	$where["sejour.type"] = " = 'urg'";
	$where["sejour.entree"] = " BETWEEN '$date_before' AND '$date_after'";
	$where["sejour.annule"] = " = '0'";
	$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
	
  $where[] = $q;
  $rpu = new CRPU();
  $rpus = $rpu->loadList($where, null, null,null, $ljoin);
  foreach($rpus as $_rpu){
    $affectations[$_rpu->_ref_rpu->box_id] = "1";
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("chambre", $chambre);
$smarty->assign("patient", $patient);
$smarty->assign("affectations", $affectations);

$smarty->display("inc_choice_lit.tpl");
?>