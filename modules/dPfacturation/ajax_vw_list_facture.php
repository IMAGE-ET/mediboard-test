<?php
/**
 * $Id: vw_factures_etab.php 18342 2013-03-07 14:04:23Z lryo $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 18342 $
 */

CCanDo::checkEdit();
$date_min           = CValue::getOrSession("_date_min", CMbDT::date());
$date_max           = CValue::getOrSession("_date_max", CMbDT::date());
$date_max           = CValue::getOrSession("_date_max", CMbDT::date());


// Praticien selectionné
$chirSel = CValue::getOrSession("chir", "-1");

// Liste des chirurgiens
$user = new CMediusers();

//Tri des factures ayant un chir dans une de ses consultations
$factures= array();
$facture = new CFactureEtablissement();

$where = array();
$where["temporaire"] = " = '0'";
$where["cloture"] = "BETWEEN '$date_min' AND '$date_max'";

if ($chirSel) {
  $where["praticien_id"] =" = '$chirSel' ";
  $factures = $facture->loadList($where, "cloture DESC", 100);
}
else {
  $factures = $facture->loadList($where , "ouverture ASC", 50);
}

//Affichage uniquement des factures qui contiennent des actes
foreach ($factures as $key => $_facture) {
  $_facture->loadRefPatient();
  $_facture->loadRefsItems();
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
if (count($factures)) {
  $facture = reset($factures);
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
