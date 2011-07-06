<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$receiver = new CDestinataireHprim();
$receiver->message = "patients";
$receivers = $receiver->loadMatchingList();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("receivers", $receivers);
$smarty->display("configure.tpl");

?>