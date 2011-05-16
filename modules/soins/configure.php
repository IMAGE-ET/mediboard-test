<?php /* $Id: configure.php */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$service  = new CService;
$services = $service->loadListWithPerms(PERM_READ);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("services", $services);
$smarty->display("configure.tpl");

?>