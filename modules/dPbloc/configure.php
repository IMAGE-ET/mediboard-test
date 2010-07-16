<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$hours = range(0, 23);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("hours", $hours);
$smarty->display("configure.tpl");
