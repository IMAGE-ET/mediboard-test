<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$prestation_id = CValue::getOrSession("prestation_id");
$object_class  = CValue::getOrSession("object_class");
$item_id       = CValue::get("item_id");

$prestation = new $object_class;
$prestation->load($prestation_id);
$prestation->loadRefsNotes();

if (!$prestation->_id) {
  $prestation->group_id = CGroups::loadCurrent()->_id;
}

$smarty = new CSmartyDP;

$smarty->assign("prestation", $prestation);
$smarty->assign("item_id"   , $item_id);
$smarty->display("inc_edit_prestation.tpl");

