<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$lot_id = CValue::get('lot_id');
$dmi_id = CValue::get('dmi_id');

$lot = new CProductOrderItemReception;
$lot->load($lot_id);

$dmi = new CDMI;
$dmi->load($dmi_id);

$smarty = new CSmartyDP();
$smarty->assign("lot", $lot);
$smarty->assign("dmi", $dmi);
$smarty->display("inc_apply_dmi.tpl");