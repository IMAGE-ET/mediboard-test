<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$type      = CValue::get("type");
$object_id = CValue::get("object_id");
$usage     = CValue::get("usage", 0);

$besoin = new CBesoinRessource();
$besoin->$type = $object_id;
/** @var CBesoinRessource[] $besoins */
$besoins = $besoin->loadMatchingList();
CMbObject::massLoadFwdRef($besoins, "type_ressource_id");

$operation = new COperation;
$operation->load($object_id);
$operation->loadRefPlageOp();
$deb_op = $operation->_datetime;
$fin_op  = CMbDT::addDateTime($operation->temp_operation, $deb_op);

foreach ($besoins as $_besoin) {
  $_besoin->loadRefTypeRessource();
  $_besoin->loadRefUsage();
  // Côté protocole, rien à vérifier
  if ($type != "operation_id") {
    $_besoin->_color = "";
    continue;
  }

  $_besoin->isAvailable();
}

$smarty = new CSmartyDP;

$smarty->assign("besoins", $besoins);
$smarty->assign("object_id", $object_id);
$smarty->assign("type"   , $type);
$smarty->assign("usage"  , $usage);

$smarty->display("inc_edit_besoins.tpl");
