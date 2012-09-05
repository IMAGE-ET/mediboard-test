<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$token = new CViewAccessToken;
$tokens = $token->loadList();

$smarty = new CSmartyDP();
$smarty->assign("tokens", $tokens);
$smarty->display("inc_list_tokens.tpl");
