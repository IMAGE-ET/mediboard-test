<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 10467 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$receiver = new CDestinataireHprim();
$receivers = $receiver->loadMatchingList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("receivers", $receivers);
$smarty->display("configure.tpl");

