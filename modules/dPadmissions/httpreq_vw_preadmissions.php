<?php /* $Id: httpreq_vw_admissions.php 7207 2009-11-03 12:03:30Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 7207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Initialisation de variables

$order_col_pre = CValue::getOrSession("order_col_pre", "heure");
$order_way_pre = CValue::getOrSession("order_way_pre", "ASC");
$date          = CValue::getOrSession("date", mbDate());
$next          = mbDate("+1 DAY", $date);

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:59", $date);

// Récupération de la liste des anesthésistes
$mediuser = new CMediusers;
$anesthesistes = $mediuser->loadAnesthesistes(PERM_READ);

$consult = new CConsultation();

// Récupération des consultation d'anesthésie du jour
$ljoin = array();
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$ljoin["patients"]     = "consultation.patient_id = patients.patient_id";
$where = array();
$where["consultation.patient_id"] = "IS NOT NULL";
$where["consultation.annule"] = "= '0'";
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($anesthesistes));
$where["plageconsult.date"] = "= '$date'";
if ($order_col_pre == "patient_id"){
  $order = "patients.nom $order_way_pre, patients.prenom $order_way_pre, consultation.heure";
} else {
  $order = "consultation.".$order_col_pre." ".$order_way_pre;
}

$listConsultations = $consult->loadList($where, $order, null, null, $ljoin);

foreach ($listConsultations as $_consult) {
  $_consult->loadRefPatient();
  $_consult->loadRefPlageconsult();
  $_consult->_ref_chir->loadRefFunction();
  $_consult->loadRefConsultAnesth();
  $_consult->_ref_consult_anesth->loadRefOperation();
  $_sejour = $_consult->_ref_consult_anesth->_ref_sejour;
  if ($_sejour->_id) {
    $_sejour->loadRefPatient();
    $_sejour->loadRefPraticien();
    $_sejour->loadNDA();
    $_sejour->loadRefsNotes();
    $_sejour->countPrestationsSouhaitees();
    $_sejour->loadRefsOperations();
    $_sejour->loadRefsAffectations();
    foreach($_sejour->_ref_affectations as $_aff) {
      $_aff->loadView();
    }
    $_sejour->getDroitsCMU();
  } 
  else {
    $next = $_consult->_ref_patient->getNextSejourAndOperation($_consult->_ref_plageconsult->date);
    if ($next["COperation"]->_id) {
      $next["COperation"]->loadRefSejour();
      $next["COperation"]->_ref_sejour->loadRefPraticien();
      $next["COperation"]->_ref_sejour->loadNDA();
      $next["COperation"]->_ref_sejour->loadRefsNotes();
    } 
    if ($next["CSejour"]->_id) {
      $next["CSejour"]->loadRefPraticien();
      $next["CSejour"]->loadNDA();
      $next["CSejour"]->loadRefsNotes();
    }
    $_consult->_next_sejour_and_operation = $next;
  }
}

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);

$smarty->assign("date_min"         , $date_min);
$smarty->assign("date_max"         , $date_max);
$smarty->assign("date_demain"      , $date_demain);
$smarty->assign("date_actuelle"    , $date_actuelle);
$smarty->assign("date"             , $date);
$smarty->assign("order_col_pre"    , $order_col_pre);
$smarty->assign("order_way_pre"    , $order_way_pre);
$smarty->assign("listConsultations", $listConsultations);
$smarty->assign("prestations"      , $prestations);
$smarty->assign("canAdmissions"    , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"      , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp"    , CModule::getCanDo("dPplanningOp"));

$smarty->display("inc_vw_preadmissions.tpl");

?>