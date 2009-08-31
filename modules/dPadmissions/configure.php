<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$hours = range(0, 23);
$intervals = array("5","10","15","20","30");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hours"     , $hours);
$smarty->assign("intervals" , $intervals);

$smarty->display("configure.tpl");
?>