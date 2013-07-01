<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
CCanDo::checkEdit();
$date_min       = CValue::getOrSession("_date_min", CMbDT::date());
$date_max       = CValue::getOrSession("_date_max", CMbDT::date());
$type_relance   = CValue::get("type_relance", 0);
$facture_class  = CValue::get("facture_class");
$chirSel        = CValue::getOrSession("chir", "-1");

$where = array();
// Praticien selectionné
if ($chirSel) {
  $where["praticien_id"] =" = '$chirSel' ";
}
if ($type_relance) {
  $where["cloture"] = " <= '$date_max'";
}
else {
  $where["cloture"] = "BETWEEN '$date_min' AND '$date_max'";
}

/** @var CFactureCabinet|CFactureEtablissement $facture*/
$facture = new $facture_class;
$factures = $facture->loadList($where , "cloture DESC", 50);

foreach ($factures as $_facture) {
  /** @var CFacture $_facture*/
  $_facture->loadRefPatient();
}

//Affichage uniquement des factures relançables
if ($type_relance) {
  foreach ($factures as $key => $_facture) {
    /** @var CFactureCabinet|CFactureEtablissement $_facture*/
    $_facture->loadRefsObjects();
    $_facture->loadRefsReglements();
    $_facture->loadRefsRelances();
    $not_exist_objets = !count($_facture->_ref_consults) && !count($_facture->_ref_sejours);
    if (!$_facture->_is_relancable || count($_facture->_ref_relances)+1 < $type_relance || $not_exist_objets) {
      unset($factures[$key]);
    }
  }
}

if (count($factures)) {
  $facture->load(reset($factures)->_id);
  $facture->loadRefPatient();
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
  $facture->loadRefPraticien();
  $facture->loadRefAssurance();
  $facture->loadRefsObjects();
  $facture->loadRefsReglements();
  $facture->loadRefsRelances();
  $facture->loadRefsNotes();
}

$reglement = new CReglement();
$banque = new CBanque();
$banques = $banque->loadList(null, "nom");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("banques"       , $banques);
$smarty->assign("facture"       , $facture);

$smarty->display("vw_list_factures.tpl");
