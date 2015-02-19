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
$date_min           = CValue::getOrSession("_date_min", "01/01/1970");
$date_max           = CValue::getOrSession("_date_max", CMbDT::date());
$etat               = CValue::getOrSession("etat", "ouvert");
$etat_cloture       = CValue::getOrSession("etat_cloture", 0);
$etat_cotation      = CValue::getOrSession("etat_cotation", 0);
$etat_relance       = CValue::getOrSession("etat_relance", 0);
$facture_id         = CValue::getOrSession("facture_id");
$patient_id         = CValue::getOrSession("patient_id");
$no_finish_reglement= CValue::getOrSession("no_finish_reglement", 0);
$type_date_search   = CValue::getOrSession("type_date_search", "ouverture");
$chirSel            = CValue::getOrSession("chirSel", "-1");
$num_facture        = CValue::getOrSession("num_facture");
$numero             = CValue::getOrSession("numero", 0);
$search_easy        = CValue::getOrSession("search_easy", 0);
$xml_etat           = CValue::getOrSession("xml_etat" , "");
$page               = CValue::get("page", "0");

// Liste des chirurgiens
$user = new CMediusers();
$listChir =  $user->loadPraticiens(PERM_EDIT);

//Patient sélectionné
$patient = new CPatient();
$patient->load($patient_id);

$ljoin = array();
$where = array();
if ($etat_relance || $search_easy == 7) {
  $ljoin["facture_relance"] = "facture_relance.object_id = facture_etablissement.facture_id";
  $where["facture_relance.object_class"] = " = 'CFactureEtablissement'";
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
if ($chirSel == -1) {
  $where["praticien_id"] = CSQLDataSource::prepareIn(array_keys($listChir));
}
elseif ($chirSel) {
  $where["praticien_id"] =" = '$chirSel' ";
}
if ($patient_id) {
  $where["patient_id"] =" = '$patient_id' ";
}
if ($num_facture) {
  $where["facture_id"] =" = '$num_facture' ";
}
if ($numero && !CAppUI::conf("dPfacturation Other use_search_easy") && ($etat_relance || $search_easy == 7)) {
  $where["facture_relance.numero"] =" = '$numero'";
}
if ($search_easy == 5) {
  $where["annule"] =" = '1'";
}
if ($search_easy == 1) {
  $where["definitive"] =" = '1'";
}
if ($xml_etat != "") {
  $where["facture"] =" = '$xml_etat' ";
}
$facture = new CFactureEtablissement();
$total_factures = $facture->countList($where, null, $ljoin);
$factures = $facture->loadList($where , "ouverture ASC, numero", "$page, 25", "facture_id", $ljoin);

//Affichage uniquement des factures qui contiennent des séjours
foreach ($factures as $key => $_facture) {
  /** @var CFacture $_facture*/
  $_facture->loadRefPatient();
  $_facture->loadRefsItems();
  $_facture->loadRefsObjects();
  $nb_tarmed  = count($_facture->_ref_actes_tarmed);
  $nb_caisse  = count($_facture->_ref_actes_caisse);
  $nb_ngap    = count($_facture->_ref_actes_ngap);
  $nb_ccam    = count($_facture->_ref_actes_ccam);
  if (!count($_facture->_ref_sejours) && !count($_facture->_ref_consults)) {
    unset($factures[$key]);
  }
  elseif ($nb_tarmed == 0 && $nb_caisse == 0 && $nb_ngap == 0 && $nb_ccam == 0 && !$etat_cotation && $search_easy != 4 && $search_easy != 0) {
    unset($factures[$key]);
  }
  elseif (($nb_tarmed != 0 || $nb_caisse != 0 || $nb_ngap != 0 || $nb_ccam != 0) && $search_easy == 4) {
    unset($factures[$key]);
  }
}

if ($facture_id && isset($factures[$facture_id])) {
  $facture->load($facture_id);
  $facture->loadRefPatient();
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
  $facture->loadRefPraticien();
  $facture->loadRefAssurance();
  $facture->loadRefsObjects();
  $facture->loadRefsReglements();
  $facture->loadRefsRelances();
  $facture->loadRefsItems();
  $facture->loadRefsNotes();
}

$reglement = new CReglement();

$banque = new CBanque();
$banques = $banque->loadList(null, "nom");

$filter = new CConsultation();
$filter->_date_min = $date_min;
$filter->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tab"           , "vw_factures_etab");
$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("listChirs"     , $listChir);
$smarty->assign("chirSel"       , $chirSel);
$smarty->assign("patient"       , $patient);
$smarty->assign("banques"       , $banques);
$smarty->assign("facture"       , $facture);
$smarty->assign("etat_cloture"  , $etat_cloture);
$smarty->assign("etat_cotation" , $etat_cotation);
$smarty->assign("etat_relance"  , $etat_relance);
$smarty->assign("date"          , CMbDT::date());
$smarty->assign("filter"        , $filter);
$smarty->assign("no_finish_reglement" , $no_finish_reglement);
$smarty->assign("type_date_search"    , $type_date_search);
$smarty->assign("num_facture"   , $num_facture);
$smarty->assign("numero"        , $numero);
$smarty->assign("search_easy"   , $search_easy);
$smarty->assign("page"          , $page);
$smarty->assign("total_factures", $total_factures);
$smarty->assign("xml_etat"      , $xml_etat);

$smarty->display("vw_factures.tpl");
