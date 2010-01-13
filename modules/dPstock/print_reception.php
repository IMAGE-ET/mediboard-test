<?php /* $Id: vw_order_form.php 7211 2009-11-03 12:27:08Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7211 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

$reception_id = CValue::get('reception_id');

$reception = new CProductReception();
$reception->load($reception_id);
$reception->loadRefsBack();
$reception->loadRefsFwd();

foreach($reception->_ref_reception_items as $_reception_item){
	$_reception_item->loadRefs();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("reception", $reception);
$smarty->display('print_reception.tpl');

?>