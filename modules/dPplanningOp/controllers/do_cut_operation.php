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

$operation_id = CValue::post("operation_id");
$date         = CValue::post("date");

$operation = new COperation;
$operation->load($operation_id);

$sejour = $operation->loadRefSejour();
$sejour->entree_prevue = "";
$sejour->sortie_prevue = "";

$msg = $sejour->store();

CAppUI::setMsg($msg ? $msg : CAppUI::tr("CSejour-msg-modify"), $msg ? UI_MSG_ERROR : UI_MSG_OK);

$do = new CDoObjectAddEdit("COperation");
$do->doIt();
