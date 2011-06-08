<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

// Création du template
$smarty = new CSmartyDP("modules/dPccam");
$smarty->assign("object" , $sejour);
$smarty->display("inc_frais_divers.tpl");

?>