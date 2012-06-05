<?php /* $Id: httpreq_vw_admissions.php 11618 2011-03-20 20:22:54Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 11618 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Type d'admission
$type           = CValue::getOrSession("type");
$service_id     = CValue::getOrSession("service_id");
$type_externe   = CValue::getOrSession("type_externe", "depart");
$date           = CValue::getOrSession("date", mbDate());
$next           = mbDate("+1 DAY", $date);
$filterFunction = CValue::getOrSession("filterFunction");

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:00", $date);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

// Entrées de la journée
$sejour = new CSejour;

$group = CGroups::loadCurrent();

// Liens diverses
$ljoin["sejour"]   = "affectation.sejour_id = sejour.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"]    = "sejour.praticien_id = users.user_id";
$ljoin["lit"]      = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"]  = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"]  = "chambre.service_id = service.service_id";

// Filtre sur les services
$where["service.externe"] = "= '1'";
if($service_id) {
  $where["service.service_id"] = "= '$service_id'";
}

// Filtre sur le type du séjour
if($type == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
} elseif($type) {
  $where["sejour.type"] = " = '$type'";
} else {
  $where[] = "`sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

$where["sejour.group_id"] = "= '$group->_id'";
if($type_externe == "depart") {
  $where["affectation.entree"] = "BETWEEN '$date' AND '$next'";
} else {
  $where["affectation.sortie"] = "BETWEEN '$date' AND '$next'";
}
$where["sejour.annule"]   = "= '0'";

$affectation = new CAffectation();
$order = "entree, sortie";

$affectations = $affectation->loadList($where, $order, null, null, $ljoin);
$sejours      = CMbObject::massLoadFwdRef($affectations, "sejour_id");
$praticiens   = CMbObject::massLoadFwdRef($sejours     , "praticien_id");
$functions    = CMbObject::massLoadFwdRef($praticiens  , "function_id");
$lits         = CMbObject::massLoadFwdRef($affectations, "lit_id");
$chambres     = CMbObject::massLoadFwdRef($lits        , "chambre_id");
$services     = CMbObject::massLoadFwdRef($chambres    , "service_id");

foreach ($affectations as $affectation_id => $affectation) {
  $affectation->loadView();
  $affectation->loadRefsAffectations();
  $affectation->_ref_prev->loadView();
  $affectation->_ref_next->loadView();
  $sejour    =& $affectation->loadRefSejour();
  $praticien =& $sejour->loadRefPraticien();
  
	if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
	  continue;
  }
  
  // Chargement du patient
  $sejour->loadRefPatient();
  $sejour->_ref_patient->loadIPP();
  
  // Chargement du numéro de dossier
  $sejour->loadNDA();
  
  // Chargement des notes sur le séjour
  $sejour->loadRefsNotes();
}

// Si la fonction selectionnée n'est pas dans la liste des fonction, on la rajoute
if ($filterFunction && !array_key_exists($filterFunction, $functions)){
	$_function = new CFunctions();
	$_function->load($filterFunction);
	$functions[$filterFunction] = $_function;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"          , $hier);
$smarty->assign("demain"        , $demain);
$smarty->assign("date_min"      , $date_min);
$smarty->assign("date_max"      , $date_max);
$smarty->assign("date_demain"   , $date_demain);
$smarty->assign("date_actuelle" , $date_actuelle);
$smarty->assign("date"          , $date);
$smarty->assign("type_externe"  , $type_externe);
$smarty->assign("affectations"  , $affectations);
$smarty->assign("canAdmissions" , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"   , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp" , CModule::getCanDo("dPplanningOp"));
$smarty->assign("functions"     , $functions);
$smarty->assign("filterFunction", $filterFunction);

$smarty->display("inc_vw_permissions.tpl");

?>