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
$date_min = CValue::getOrSession("_date_min", CMbDT::date());
$date_max = CValue::getOrSession("_date_max", CMbDT::date());
$all_group_compta = CValue::getOrSession("_all_group_compta" , 1);
$prat = CValue::get("chir");

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($prat);
$praticien->loadRefBanque();
if (!$praticien->_id) {
  CAppUI::stepMessage(UI_MSG_WARNING, "CMediusers-warning-undefined");
  return;
}

// Extraction des elements qui composent le numero de compte
$compte_banque  = substr($praticien->compte,  0,  5);
$compte_guichet = substr($praticien->compte,  5,  5);
$compte_numero  = substr($praticien->compte, 10, 11);
$compte_cle     = substr($praticien->compte, 21,  2);

// Nombre de cheques remis
$nbRemise = 0;
// Montant total des cheques
$montantTotal = 0;

$where = array();
$ljoin = array();

// Chargement des règlements via les factures
$ljoin["facture_cabinet"] = "facture_cabinet.facture_id = reglement.object_id";
$where["object_class"] = " = 'CFactureCabinet'";
$where['facture_cabinet.praticien_id'] = "= '$praticien->_id'";
if (!$all_group_compta) {
  $where["facture_cabinet.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
}
$where['reglement.mode'] = "= 'cheque' ";
$where['reglement.date'] = "BETWEEN '$date_min' AND '$date_max 23:59:59' ";
$order = "reglement.date ASC";

$reglement = new CReglement();
$reglements = $reglement->loadList($where, $order, null, "reglement.reglement_id", $ljoin);

// Chargements des consultations
$montantTotal = 0.0;
foreach ($reglements as $_reglement) {
  /** @var CReglement $_reglement*/
  $_reglement->loadTargetObject()->loadRefPatient();
  $_reglement->loadRefBanque();
  $montantTotal += $_reglement->montant;
}
$nbRemise = count($reglements);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"      , $praticien);
$smarty->assign("reglements"     , $reglements);
$smarty->assign("date"           , CMbDT::date());
$smarty->assign("compte_banque"  , $compte_banque);
$smarty->assign("compte_guichet" , $compte_guichet);
$smarty->assign("compte_numero"  , $compte_numero);
$smarty->assign("compte_cle"     , $compte_cle);
$smarty->assign("montantTotal"   , $montantTotal);
$smarty->assign("nbRemise"       , $nbRemise);

$smarty->display("print_bordereau.tpl");