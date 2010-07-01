<?php /* $Id: httpreq_vw_order.php 7211 2009-11-03 12:27:08Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7211 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$reception_id = CValue::get('reception_id');

// Loads the expected Order
$reception = new CProductReception();
$reception->load($reception_id);
$reception->loadRefsBack();

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('reception', $reception);
$smarty->display('inc_reception.tpl');
