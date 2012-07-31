<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
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
$factureconsult_id  = CValue::getOrSession("factureconsult_id");
$patient_id         = CValue::getOrSession("patient_id");
$no_finish_reglement= CValue::getOrSession("no_finish_reglement", 0);

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
$facture = new CFactureConsult();

$where = array();
$where["ouverture"] = "BETWEEN '$date_min' AND '$date_max'";

if ($etat_cloture && !$etat_ouvert) {
  $where["cloture"] = "BETWEEN '$date_min' AND '$date_max'";
}
elseif ($etat_cloture && $etat_ouvert) {
   $where[] = "cloture BETWEEN '$date_min' AND '$date_max' OR cloture IS NULL";
}
elseif (!$etat_cloture && $etat_ouvert) {
  $where["cloture"] = "IS NULL";
}

if ($chirSel) {
  $ljoin = array();
  
  $ljoin["consultation"] = "factureconsult.factureconsult_id = consultation.factureconsult_id" ;
  $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id" ;
  
  $where["consultation.factureconsult_id"] =" IS NOT NULL ";
  $where["plageconsult.chir_id"] =" = '$chirSel' ";

  if ($patient_id) {
    $where["factureconsult.patient_id"] =" = '$patient_id' ";
  }
  
  $order = "factureconsult.cloture DESC";
  $limit ="0 , 30";
  
  $facture = new CFactureConsult();
  $factures = $facture->loadList($where, $order, $limit, null, $ljoin);
}
else {
  $where["patient_id"] = "= '$patient_id'";	
  $factures = $facture->loadList($where , "ouverture ASC", 50);
}

if ($no_finish_reglement) {
  foreach ($factures as $key => $_facture) {
    $_facture->loadRefReglements();
    if ($_facture->_du_patient_restant != 0 ) {
      unset($factures[$key]);
    }
  }
}

$derconsult_id = null;
$assurances_patient = array();
if ($factureconsult_id) {
  $facture->load($factureconsult_id);	
  $facture->loadRefs();
  if ($facture->_ref_consults) {
    $last_consult = end($facture->_ref_consults);
    $derconsult_id = $last_consult->_id;
  }
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
}

$reglement = new CReglement();
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null,$orderBanque);

$acte_tarmed = null;
//Instanciation d'un acte tarmed pour l'ajout de ligne dans la facture
if (CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
  $acte_tarmed = new CActeTarmed();
  $acte_tarmed->date = mbDate();
  $acte_tarmed->quantite = 1;
}

$filter = new CConsultation();
$filter->_date_min = $date_min;
$filter->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("acte_tarmed"   , $acte_tarmed);
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
$smarty->assign("no_finish_reglement"      ,$no_finish_reglement);

$smarty->display("vw_factures.tpl");
?>