<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$token_id = CValue::getOrSession("token_id");

$smarty = new CSmartyDP();
$smarty->assign("token_id", $token_id);
$smarty->display("vw_edit_tokens.tpl");
