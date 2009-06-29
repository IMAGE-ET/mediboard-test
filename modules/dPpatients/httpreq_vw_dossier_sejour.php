<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = mbGetValueFromGet("sejour_id");

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadComplete();


// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("object", $sejour);
$smarty->display('inc_vw_dossier_sejour.tpl'); 

?>