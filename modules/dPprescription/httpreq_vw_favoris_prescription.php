<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$chapitre = CValue::get("chapitre");
$praticien_id = CValue::get("praticien_id");

$favoris = CPrescription::getFavorisPraticien($praticien_id, $chapitre);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("favoris", $favoris);
$smarty->assign("chapitre", $chapitre);
$smarty->display("inc_vw_favoris_prescription.tpl");

?>