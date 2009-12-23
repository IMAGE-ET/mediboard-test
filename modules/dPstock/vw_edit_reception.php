<?php /* $Id: vw_aed_order.php 7645 2009-12-17 16:40:57Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7645 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$reception_id = CValue::get('reception_id');

$reception = new CProductReception();
$reception->load($reception_id);
$reception->loadBackRefs("reception_items");

$smarty = new CSmartyDP();
$smarty->assign('reception', $reception);
$smarty->display('vw_edit_reception.tpl');
