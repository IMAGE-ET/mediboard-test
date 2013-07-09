<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$copy_operation_id = CValue::post("copy_operation_id");
$date           = CValue::post("date");
$salle_id       = CValue::post("salle_id");
$sejour_id      = CValue::post("sejour_id");
$time_operation = CValue::post("time_operation");

$operation = new COperation();
$operation->load($copy_operation_id);

$operation->_id = $operation->_hour_urgence = $operation->_min_urgence = null;
$operation->date = $date;
$operation->salle_id = $salle_id;
$operation->sejour_id = $sejour_id;
$operation->time_operation = $time_operation;

$msg = $operation->store();

CAppUI::setMsg($msg ? $msg : CAppUI::tr("COperation-msg-modify"), $msg ? UI_MSG_ERROR : UI_MSG_OK);

CAppUI::getMsg();
CApp::rip();
