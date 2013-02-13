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
$facture = new CFactureEtablissement();

$where = array();
$where["temporaire"] = " = '0'";

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
  $where["praticien_id"] =" = '$chirSel' ";
  if ($patient_id) {
    $where["patient_id"] =" = '$patient_id' ";
  }
  $factures = $facture->loadList($where, "cloture DESC", 100);
}
else {
  $where["patient_id"] = "= '$patient_id'";  
  $factures = $facture->loadList($where , "ouverture ASC", 50);
}

//Affichage uniquement des factures qui contiennent des actes
foreach ($factures as $key => $_facture) {
  $_facture->loadRefPatient();
  $_facture->loadRefSejour();
  $nb_tarmed  = count($_facture->_ref_actes_tarmed);
  $nb_caisse  = count($_facture->_ref_actes_caisse);
  $nb_ngap    = count($_facture->_ref_actes_ngap);
  $nb_ccam    = count($_facture->_ref_actes_ccam);
  if (count($_facture->_ref_sejours) == 0) {
    unset($factures[$key]);
    $_facture->loadRefs();
  }
  elseif ($nb_tarmed == 0 && $nb_caisse == 0 && $nb_ngap == 0 && $nb_ccam == 0) {
    unset($factures[$key]);
  }
}

if ($no_finish_reglement) {
  foreach ($factures as $key => $_facture) {
    $_facture->loadRefsReglements();
    if ($_facture->_du_restant_patient != 0) {
      unset($factures[$key]);
    }
  }
}

$assurances_patient = array();
if ($facture_id) {
  $facture->load($facture_id);  
  $facture->loadRefs();
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
}

$reglement = new CReglement();

$banque = new CBanque();
$banques = $banque->loadList(null, "nom");

$filter = new CSejour();
$filter->_date_min = $date_min;
$filter->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
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
$smarty->assign("type_date_search"    , $type_date_search);

$smarty->display("vw_factures.tpl");
