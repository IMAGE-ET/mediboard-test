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

CCanDo::checkRead();

$operation_id = CValue::get("operation_id");

$operation = new COperation();
$operation->load($operation_id);
$sejour = $operation->loadRefSejour();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("operation", $operation);
$smarty->display("inc_vw_surveillance_perop_administration.tpl");
