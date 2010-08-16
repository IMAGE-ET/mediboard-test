<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadExtDiagnostics();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour);

$smarty->display("inc_diagnostic_principal.tpl");

?>