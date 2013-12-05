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
$echeance_id   = CValue::get("echeance_id");
$facture_id    = CValue::getOrSession("facture_id");
$facture_class = CValue::getOrSession("facture_class");

$echeance = new CEcheance();
$echeance->load($echeance_id);

if (!$echeance->_id) {
  $echeance->object_id    = $facture_id;
  $echeance->object_class = $facture_class;
}

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("echeance" , $echeance);

$smarty->display("vw_edit_echeance.tpl");