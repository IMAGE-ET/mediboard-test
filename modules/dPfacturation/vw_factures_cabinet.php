<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();

$date_min           = CValue::getOrSession("_date_min", mbDate());
$date_max           = CValue::getOrSession("_date_max", mbDate());
$etat               = CValue::getOrSession("etat", "ouvert");
$etat_cloture       = CValue::getOrSession("etat_cloture", 1);
$etat_ouvert        = CValue::getOrSession("etat_ouvert", 1);
$facture_id         = CValue::getOrSession("facture_id");
$patient_id         = CValue::getOrSession("patient_id");
$no_finish_reglement= CValue::getOrSession("no_finish_reglement", 0);
$type_date_search   = CValue::getOrSession("type_date_search", "cloture");

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", "-1");

//Patient sélectionné
$patient = new CPatient();
$patient->load($patient_id);

// Liste des chirurgiens
$user = new CMediusers();
$listChir =  $user->loadPraticiens(PERM_EDIT);

//Tri des factures ayant un chir dans une de ses consultations
$factures= array();
$facture = new CFactureCabinet();

$where = array();

if ($etat_cloture && !$etat_ouvert) {
  $where["$type_date_search"] = "BETWEEN '$date_min' AND '$date_max'";
}
elseif ($etat_cloture && $etat_ouvert) {
   $where[] = "$type_date_search BETWEEN '$date_min' AND '$date_max' OR $type_date_search IS NULL";
}
elseif (!$etat_cloture && $etat_ouvert) {
  $where["$type_date_search"] = "IS NULL";
}

if ($chirSel) {
  if (!CAppUI::conf("dPfacturation CFactureCabinet use_create_bill")) {
    $ljoin = array();
    $ljoin["consultation"] = "facture_cabinet.facture_id = consultation.facture_id" ;
    $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id" ;
    
    $where["consultation.facture_id"] =" IS NOT NULL ";
    $where["facture_cabinet.praticien_id"] =" = '$chirSel' ";
  
    if ($patient_id) {
      $where["facture_cabinet.patient_id"] =" = '$patient_id' ";
    }
    $factures = $facture->loadList($where, "facture_cabinet.cloture DESC", 100, null, $ljoin);
  }
  else {
    $where["praticien_id"] =" = '$chirSel' ";
    if ($patient_id) {
      $where["patient_id"] =" = '$patient_id' ";
    }
    $factures = $facture->loadList($where, "cloture DESC", 100);
  }
}
else {
  $where["patient_id"] = "= '$patient_id'";  
  $factures = $facture->loadList($where , "ouverture ASC", 50);
}

foreach ($factures as $_facture) {
  $_facture->loadRefPatient();
}

if ($no_finish_reglement) {
  foreach ($factures as $key => $_facture) {
    $_facture->loadRefsReglements();
    if ($_facture->_du_restant_patient != 0 ) {
      unset($factures[$key]);
    }
  }
}

$derconsult_id = null;
$assurances_patient = array();
if ($facture_id) {
  $facture->load($facture_id);  
  $facture->loadRefs();
  if ($facture->_ref_consults) {
    $last_consult = end($facture->_ref_consults);
    $derconsult_id = $last_consult->_id;
  }
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
}

$reglement = new CReglement();

$banque = new CBanque();
$banques = $banque->loadList(null, "nom");

$filter = new CConsultation();
$filter->_date_min = $date_min;
$filter->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("derconsult_id" , $derconsult_id);
$smarty->assign("listChirs"     , $listChir);
$smarty->assign("chirSel"       , $chirSel);
$smarty->assign("patient"       , $patient);
$smarty->assign("banques"       , $banques);
$smarty->assign("facture"       , $facture);
$smarty->assign("etat_ouvert"   , $etat_ouvert);
$smarty->assign("etat_cloture"  , $etat_cloture);
$smarty->assign("date"          , mbDate());
$smarty->assign("filter"        , $filter);
$smarty->assign("no_finish_reglement" ,$no_finish_reglement);
$smarty->assign("type_date_search"    ,$type_date_search);

$smarty->display("vw_factures.tpl");
