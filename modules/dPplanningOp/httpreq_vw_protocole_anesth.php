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

CCanDo::checkEdit();

$prescription_id = CValue::getOrSession("prescription_id");

$prescription = new CPrescription();
$prescription->load($prescription_id);

$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("nodebug", true);
$smarty->display("inc_vw_protocole_anesth.tpl");
