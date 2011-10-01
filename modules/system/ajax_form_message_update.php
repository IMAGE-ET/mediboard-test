<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$smarty = new CSmartyDP();
$smarty->assign("message", new CMessage);
$smarty->display("inc_form_message_update.tpl");
