<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$chapitre        = CValue::get("chapitre");
$praticien_id    = CValue::get("praticien_id");
$prescription_id = CValue::get("prescription_id");
$mode_protocole  = CValue::get("mode_protocole");
$mode_pharma     = CValue::get("mode_pharma");

$favoris = CPrescription::getFavorisPraticien($praticien_id, $chapitre);

$user = new CMediusers();
$user->load($praticien_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("chapitre", $chapitre);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("favoris", $favoris);
$smarty->assign("user", $user);
$smarty->assign("mode_protocole", $mode_protocole);
$smarty->assign("mode_pharma", $mode_pharma);
$smarty->display("inc_select_favoris_prescription.tpl");

?>