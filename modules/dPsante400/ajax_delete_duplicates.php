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
$id400        = CValue::get("id400");

$idSante400 = new CIdSante400();
$where = array(
  "object_id"    => "= '$object_id'",
  "object_class" => "= '$object_class'",
  "tag"          => "= '$tag'",
  "id400"        => "= '$id400'",
);

$order = "last_update DESC";

$listIdSante400 = $idSante400->loadList($where, $order);

$survivor = reset($listIdSante400)->_id;

foreach($listIdSante400 as $idSante400) {
  if($idSante400->_id != $survivor) {
    if($msg = $idSante400->delete()) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    } else {
      CAppUI::setMsg("Identifiant supprimé", UI_MSG_OK);
    }
  }
}

echo CAppUI::getMsg();

?>
