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
$num_facture        = CValue::getOrSession("num_facture", "");
$numero             = CValue::getOrSession("numero", "1");
$search_easy        = CValue::getOrSession("search_easy", "0");
$page               = CValue::get("page", "0");
$facture_class      = CValue::getOrSession("facture_class", "CFactureCabinet");
$xml_etat           = CValue::getOrSession("xml_etat", "");

// Liste des chirurgiens
$user = new CMediusers();
$listChir =  $user->loadPraticiens(PERM_EDIT);

$ljoin = array();
$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
if ($etat_relance || $search_easy == 7) {
  $ljoin["facture_relance"] = "facture_relance.object_id = facture_etablissement.facture_id";
  $where["facture_relance.object_class"] = " = '$facture_class'";
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
if ($numero && !CAppUI::conf("dPfacturation Other use_search_easy")) {
  $where["numero"] =" = '$numero'";
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

$facture = new $facture_class;
$factures = $facture->loadList($where , "ouverture ASC, numero", "$page, 25", "facture_id", $ljoin);
$total_factures = $facture->countList($where, null, $ljoin);

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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("facture"       , $facture);
$smarty->assign("page"          , $page);
$smarty->assign("total_factures" , $total_factures);

$smarty->display("inc_list_factures.tpl");