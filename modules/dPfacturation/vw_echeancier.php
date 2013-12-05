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
$facture_id    = CValue::getOrSession("facture_id");
$facture_class = CValue::getOrSession("facture_class");

$facture = new $facture_class;
$facture->load($facture_id);

$echeance = new CEcheance();
$echeance->object_id    = $facture_id;
$echeance->object_class = $facture_class;
$echeances = $echeance->loadMatchingList();

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("echeance" , $echeance);
$smarty->assign("echeances", $echeances);
$smarty->assign("facture"  , $facture);

$smarty->display("vw_echeancier.tpl");