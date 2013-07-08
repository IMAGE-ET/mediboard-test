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
$date_min       = CValue::getOrSession("_date_min", CMbDT::date());
$date_max       = CValue::getOrSession("_date_max", CMbDT::date());
$type_relance   = CValue::get("type_relance");
$facture_class  = CValue::get("facture_class");
$etat_relance   = CValue::get("etat_relance");
$chirSel        = CValue::getOrSession("chir", "-1");

$facture = new $facture_class;
$spec = $facture->getSpec();

$ljoin = array();
$ljoin["facture_relance"] = "facture_relance.object_id = ".$spec->table.".facture_id";

$where = array();
$where["facture_relance.object_class"] = " = '$facture_class'";
$where["facture_relance.date"] = "BETWEEN '$date_min' AND '$date_max'";
$where["facture_relance.numero"] = " = '$type_relance'";
if ($etat_relance) {
  $where["facture_relance.etat"] = " = '$etat_relance'";
}
// Praticien selectionné
if ($chirSel) {
  $where["praticien_id"] =" = '$chirSel' ";
}
$factures = $facture->loadList($where, "ouverture", null, null, $ljoin);

foreach ($factures as $_facture) {
  $_facture->loadRefPatient();
  $_facture->loadRefsObjects();
}

if (count($factures)) {
  $facture = reset($factures);
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
