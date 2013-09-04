<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$user_id      = CValue::get("user_id");
$object_class = CValue::get("object_class");
$id_min       = CValue::get("id_min");
$id_max       = CValue::get("id_max");
$date_min     = CValue::get("_date_min");
$date_max     = CValue::get("_date_max");
$step         = CValue::get("step");
$simulate     = CValue::get("simulate");

$user_log = new CUserLog();
$where = array();

$where["object_class"] = "= '$object_class'";
$where["date"] = "BETWEEN '$date_min' AND '$date_max'";
$where['type'] = "= 'create'";
$where["object_id"] = "BETWEEN '$id_min' AND '$id_max'";
if ($user_id) {
  $where["user_id"] = "= '$user_id'";
}

// Mode simulation (on compte les objets correspondants
if ($simulate) {
  $nb_objects = $user_log->countList($where);

  CAppUI::stepAjax("Il y a $nb_objects qui correspondent à la recherche");
  CApp::rip();
}

// Purge réelle
/** @var CUserLog[] $user_logs */
$user_logs = $user_log->loadList($where, null, $step);

foreach ($user_logs as $_user_log) {
  $object = $_user_log->loadTargetObject();
  $object_id = $object->_id;
  $msg = $object->purge();
  CAppUI::setMsg($msg ? $msg : "Objet $object_id supprimé", $msg ? UI_MSG_ERROR : UI_MSG_OK);
}

echo CAppUI::getMsg();