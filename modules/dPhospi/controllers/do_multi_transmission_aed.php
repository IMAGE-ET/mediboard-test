<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do_data = new CDoObjectAddEdit("CTransmissionMedicale");
$do_data->doBind();
if ($do_data->_obj->_text_data) {
  $do_data->_obj->text = $do_data->_obj->_text_data;
  $do_data->_obj->type = "data";
  $do_data->doStore();
}

$do_action = new CDoObjectAddEdit("CTransmissionMedicale");
$do_action->doBind();
if ($do_action->_obj->_text_action) {
  $do_action->_obj->text = $do_action->_obj->_text_action;
  $do_action->_obj->type = "action";
  $do_action->doStore();
}

$do_result = new CDoObjectAddEdit("CTransmissionMedicale");
$do_result->doBind();
if ($do_result->_obj->_text_result) {
  $do_result->_obj->text = $do_result->_obj->_text_result;
  $do_result->_obj->type = "result";
  $do_result->doStore();
}

$do_data->doRedirect();
?>