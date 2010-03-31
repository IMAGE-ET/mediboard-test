<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>