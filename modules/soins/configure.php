<?php /* $Id: configure.php */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();


// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>