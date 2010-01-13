<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

// Chargement des receptions de l'etablissement
$reception = new CProductReception();
$reception->group_id = CGroups::loadCurrent()->_id;
$receptions = $reception->loadMatchingList();

foreach($receptions as $_reception){
	$_reception->countReceptionItems();
}
// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("reception", $reception);
$smarty->assign("receptions", $receptions);
$smarty->display('vw_idx_reception.tpl');

?>