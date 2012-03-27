<?php /* $Id: export_evtServeurActivitePmsi.php 12577 2011-07-05 14:05:14Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$object = new $object_class;
$object->load($object_id);
$object->countExchanges();

// Facturation de l'opération où du séjour
$object->facture = 0;
if ($msg = $object->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
  
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("../../dPpmsi/templates/inc_export_actes_pmsi.tpl");

?>