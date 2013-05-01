<?php /* $Id: httpreq_do_add_ccam.php 8262 2010-03-08 17:26:03Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: 8262 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");
$tag          = CValue::get("tag");
$idex_value   = CValue::get("id400");

$where = array(
  "object_id"    => "= '$object_id'",
  "object_class" => "= '$object_class'",
  "tag"          => "= '$tag'",
  "id400"        => "= '$idex_value'",
);

$order = "last_update DESC";

$idex  = new CIdSante400();
$idexs = $idex->loadList($where, $order);

$survivor = reset($idexs)->_id;

foreach ($idexs as $_idex) {
  if ($_idex->_id != $survivor) {
    if ($msg = $_idex->delete()) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    }
    else {
      CAppUI::setMsg("Identifiant supprimé", UI_MSG_OK);
    }
  }
}

echo CAppUI::getMsg();