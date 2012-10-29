<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 14661 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$message = CValue::getOrSession("message");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("message", $message);
$smarty->display("vw_display_hprim_message.tpl");
