<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

if (!$can->read && !$dialog) {
  $can->redirect();
}
// Création du template
$smarty = new CSmartyDP();

$smarty->display("view_install.tpl");

?>