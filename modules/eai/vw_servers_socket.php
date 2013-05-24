<?php /* $Id $ */

/**
 * View server socket EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$processes = CSocketBasedServer::getPsStatus();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("processes", $processes);
$smarty->display("vw_servers_socket.tpl");

