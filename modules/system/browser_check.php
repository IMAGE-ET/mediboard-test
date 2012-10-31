<?php /* $Id: about.php 10973 2010-12-27 21:51:09Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10973 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::check();

$smarty = new CSmartyDP();
$smarty->display("browser_check.tpl");
?>
