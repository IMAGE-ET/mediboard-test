<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$del = CValue::post("del");
$callback = CValue::post("callback");
$data_id = CValue::post("data_id");
$action_id = CValue::post("action_id");
$result_id = CValue::post("result_id");
$locked    = CValue::post("_locked");

$do = new CDoObjectAddEdit("CTransmissionMedicale", "data_id");
$_POST["transmission_medicale_id"] = isset($_POST["data_id"]) ? $_POST["data_id"] : "";
$do->doBind();
if ($del && $data_id) {
  $do->doDelete();
}
else if ($do->_obj->_text_data) {
  $do->_obj->text = $do->_obj->_text_data;
  $do->_obj->type = "data";

  $do->doStore();
  if (!$_POST["_text_action"] && !$_POST["_text_result"] && $locked) {
    $do->_obj->locked = 1;
    $do->_obj->store();
  }
}


$do = new CDoObjectAddEdit("CTransmissionMedicale", "action_id");
$_POST["transmission_medicale_id"] = isset($_POST["action_id"]) ? $_POST["action_id"] : "";
$do->doBind();
if ($del && $action_id) {
  $do->doDelete();
}
else if ($do->_obj->_text_action) {
  $do->_obj->text = $do->_obj->_text_action;
  $do->_obj->type = "action";
  $do->doStore();
  if (!$_POST["_text_result"] && $locked) {
    $do->_obj->locked = 1;
    $do->_obj->store();
  }
}


$do = new CDoObjectAddEdit("CTransmissionMedicale", 'result_id');
$_POST["transmission_medicale_id"] = isset($_POST["result_id"]) ? $_POST["result_id"] : "";
$do->doBind();
if ($del && $result_id) {
  $do->doDelete();
}
else if ($do->_obj->_text_result) {
  $do->_obj->text = $do->_obj->_text_result;
  $do->_obj->type = "result";
  $do->doStore();
  if ($locked) {
    $do->_obj->locked = 1;
    $do->_obj->store();
  }
}

$do->callBack = $callback;
$do->ajax = 1;
$do->doRedirect();
