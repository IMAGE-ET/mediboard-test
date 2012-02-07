<?php /* $Id: configure.php */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$listHours = range(0, 23);
foreach($listHours as &$_hour){
  $_hour = str_pad($_hour,2,"0",STR_PAD_LEFT);
}

$service  = new CService;
$services = $service->loadListWithPerms(PERM_READ);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("services", $services);
$smarty->assign("listHours" , $listHours);

$smarty->display("configure.tpl");

?>