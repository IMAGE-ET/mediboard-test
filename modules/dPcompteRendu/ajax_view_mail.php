<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$type = CValue::get("type", "doc");

$smarty = new CSmartyDP;
$smarty->assign("type", $type);
$smarty->display("inc_view_mail.tpl");

?>