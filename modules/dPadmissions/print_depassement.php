<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$id = mbGetValueFromGetOrSession("id");

$admission = new COperation();
$admission->load($id);
$admission->loadRefs();
$admission->_ref_sejour->loadRefsFwd();
$admission->_ref_plageop->loadRefs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("admission", $admission);

$smarty->display("print_depassement.tpl");

?>