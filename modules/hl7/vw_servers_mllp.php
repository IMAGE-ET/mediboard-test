<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$processes = CMLLPServer::getPsStatus();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("processes", $processes);
$smarty->display("vw_servers_mllp.tpl");

?>