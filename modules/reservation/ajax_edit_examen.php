<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$examen_operation_id = CValue::get("examen_operation_id");

$examen_op = new CExamenOperation();
$examen_op->load($examen_operation_id);

$smarty = new CSmartyDP();

$smarty->assign("examen_op", $examen_op);

$smarty->display("inc_edit_examen.tpl");
