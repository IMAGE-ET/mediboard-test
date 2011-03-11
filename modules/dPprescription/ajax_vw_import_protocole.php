<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$smarty = new CSmartyDP;

$smarty->assign("praticien_id", CValue::get("praticien_id"));
$smarty->assign("function_id" , CValue::get("function_id"));
$smarty->assign("group_id"    , CValue::get("group_id"));

$smarty->display("inc_vw_import_protocole.tpl");

?>