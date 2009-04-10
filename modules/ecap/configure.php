<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

CMedicap::makeTags();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("paths", CMedicap::$paths);
$smarty->assign("tags", CMedicap::$tags);
$smarty->display("configure.tpl");

?>