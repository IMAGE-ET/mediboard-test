<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_class        = CValue::post("object_class");
$object_id           = CValue::post("object_id");
$type                = CValue::post("type");
$daily_check_list_id = CValue::post("daily_check_list_id");

// On recherche une check list déja remplie, pour éviter les doublons
if (!$daily_check_list_id && in_array($object_class, CDailyCheckList::$_HAS_classes)) {
  /** @var COperation|CPoseDispositifVasculaire $object */
  $object = new $object_class;
  $object->load($object_id);

  $list = CDailyCheckList::getList($object, null, $type);

  if ($list->_id) {
    $_POST["daily_check_list_id"] = $list->_id;
  }
}

$do = new CDoObjectAddEdit("CDailyCheckList");
$do->doIt();
