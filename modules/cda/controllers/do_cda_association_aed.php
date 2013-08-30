<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$select     = CValue::post("select");
$group_type = CValue::post("group_type");
$group_id   = CValue::post("group_id");
$group      = new CGroups();
$group->load($group_id);

foreach ($select as $_id => $_type_code) {
  $category = new CFilesCategory();
  $category->load($_id);
  $idex = new CIdSante400();
  $idex->tag = "cda_association_code_$group_id";
  $idex->setObject($category);
  $idex->loadMatchingObject();
  $idex->last_update = CMbDT::dateTime();
  $idex->id400 = $_type_code;

  if (!$_type_code && $idex->_id) {
    $idex->delete();
    continue;
  }

  if ($_type_code && $msg = $idex->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }

}

$idex = new CIdSante400();
$idex->tag = "cda_association_code";
$idex->setObject($group);
$idex->loadMatchingObject();
$idex->last_update = CMbDT::dateTime();
$idex->id400 = $group_type;

if ($group_type && $msg = $idex->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}


CAppUI::setMsg("Configuration effectué");