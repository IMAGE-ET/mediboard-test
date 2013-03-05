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
$patient_id     = CValue::getOrSession("patient_id");
$consult_id     = CValue::get("consult_id");
$type_facture   = CValue::get("type_facture", "maladie");
$chirsel_id     = CValue::get("executant_id");
$date           = CValue::get("date", mbDate());

$facture = new CFactureCabinet();
$facture->ajoutConsult($patient_id, $chirsel_id, $consult_id, $type_facture);
$facture->loadRefs();

// Chargement des banques
$orderBanque = "nom ASC";
$banque      = new CBanque();
$banques     = $banque->loadList(null,$orderBanque);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"     , null);
$smarty->assign("reglement"   , new CReglement());
$smarty->assign("banques"     , $banques);
$smarty->assign("facture"     , $facture);
if (!CValue::get("not_load_banque")) {
  $smarty->assign("factures"      , array(new CFactureCabinet()));
}
$smarty->assign("derconsult_id" , $consult_id);
$smarty->assign("date"          , $date);
$smarty->assign("chirSel"       , $chirsel_id);

$smarty->display("inc_vw_facturation.tpl");
?>