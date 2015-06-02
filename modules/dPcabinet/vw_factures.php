<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$rectif             = CMbDT::transform("+0 DAY", CMbDT::date(), "%d")-1;
$month_deb          = CMbDT::date("-$rectif DAYS"  , CMbDT::date());
$date_min           = CValue::getOrSession("_date_min", $month_deb);
$date_max           = CValue::getOrSession("_date_max", CMbDT::date());
$etat               = CValue::getOrSession("etat", "ouvert");
$etat_cloture       = CValue::getOrSession("etat_cloture" , 0);
$etat_relance       = CValue::getOrSession("etat_relance" , 0);
$facture_id         = CValue::getOrSession("facture_id");
$patient_id         = CValue::getOrSession("patient_id");
$no_finish_reglement= CValue::getOrSession("no_finish_reglement", 0);
$type_date_search   = CValue::getOrSession("type_date_search", "cloture");
$chirSel            = CValue::getOrSession("chirSel", "-1");
$num_facture        = CValue::getOrSession("num_facture", "");
$numero             = CValue::getOrSession("numero", 0);
$search_easy        = CValue::getOrSession("search_easy", "0");
$xml_etat           = CValue::getOrSession("xml_etat", "");

// Liste des chirurgiens
$user = new CMediusers();
$listChir =  $user->loadPraticiens(PERM_EDIT);

//Patient sélectionné
$patient = new CPatient();
$patient->load($patient_id);

$ljoin = array();
$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
if ($etat_relance || $search_easy == 7) {
  $ljoin["facture_relance"] = "facture_relance.object_id = facture_cabinet.facture_id";
  $where["facture_relance.object_class"] = " = 'CFactureCabinet'";
}

$where["$type_date_search"] = "BETWEEN '$date_min' AND '$date_max'";
if (($etat_cloture == "1" || $search_easy == 3) && $type_date_search != "cloture") {
  $where["cloture"] = "IS NULL";
}
elseif (($etat_cloture == "2" || $search_easy == 2) && $type_date_search != "cloture") {
  $where["cloture"] = "IS NOT NULL";
}

if ($no_finish_reglement || $search_easy == 6) {
  $where["patient_date_reglement"] = "IS NOT NULL";
}
$where["praticien_id"] = $chirSel ? " = '$chirSel'" : CSQLDataSource::prepareIn(array_keys($listChir));
if ($patient_id) {
  $where["patient_id"] =" = '$patient_id'";
}
if ($numero && !CAppUI::conf("dPfacturation Other use_search_easy") && ($etat_relance || $search_easy == 7)) {
  $where["facture_relance.numero"] =" = '$numero'";
}
if ($search_easy == 1) {
  $where["definitive"] =" = '1'";
}

if ($num_facture) {
  $ljoin = array();
  $where = array();
  $where["facture_id"] =" = '$num_facture' ";
}
if ($xml_etat != "") {
  $where["facture"] =" = '$xml_etat' ";
}

$facture = new CFactureCabinet();
$factures = $facture->loadList($where , "ouverture ASC", "0, 25", null, $ljoin);
$total_factures = $facture->countMultipleList($where, "facture_id", $ljoin);
$total_factures = $total_factures[0]['total'];

foreach ($factures as $key => $_facture) {
  /* @var CFactureCabinet $_facture*/
  $_facture->loadRefPatient();
  $_facture->loadRefsItems();
  $_facture->loadRefsConsultation();
  $nb_tarmed  = count($_facture->_ref_actes_tarmed);
  $nb_caisse  = count($_facture->_ref_actes_caisse);
  $nb_ngap    = count($_facture->_ref_actes_ngap);
  $nb_ccam    = count($_facture->_ref_actes_ccam);
  if (count($_facture->_ref_consults) == 0) {
    unset($factures[$key]);
  }
}

$derconsult_id = null;
if ($facture_id && isset($factures[$facture_id])) {
  $facture->load($facture_id);
  $facture->loadRefPatient();
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
  $facture->loadRefPraticien();
  $facture->loadRefAssurance();
  $facture->loadRefsObjects();
  $facture->loadRefsReglements();
  $facture->loadRefsRelances();
  $facture->loadRefsNotes();
  if ($facture->_ref_consults) {
    $derconsult_id = $facture->_ref_last_consult->_id;
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

$smarty->assign("tab"           , "vw_factures_cabinet");
$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("derconsult_id" , $derconsult_id);
$smarty->assign("listChirs"     , $listChir);
$smarty->assign("chirSel"       , $chirSel);
$smarty->assign("patient"       , $patient);
$smarty->assign("banques"       , $banques);
$smarty->assign("facture"       , $facture);
$smarty->assign("etat_cloture"  , $etat_cloture);
$smarty->assign("etat_relance"  , $etat_relance);
$smarty->assign("date"          , CMbDT::date());
$smarty->assign("filter"        , $filter);
$smarty->assign("no_finish_reglement" , $no_finish_reglement);
$smarty->assign("type_date_search"    , $type_date_search);
$smarty->assign("num_facture"   , $num_facture);
$smarty->assign("numero"        , $numero);
$smarty->assign("search_easy"   , $search_easy);
$smarty->assign("page"          , 0);
$smarty->assign("total_factures", $total_factures);
$smarty->assign("xml_etat"      , $xml_etat);

$smarty->display("vw_factures.tpl");
