<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$lettre        = CValue::get("lettre");
$codeATC       = CValue::get("codeATC");
$code_cip      = CValue::get("code_cip");
$function_guid = CValue::get("function_guid", null);

$crc = "";

if ($function_guid) {
  $crc = $function_guid;
}
else {
  $crc = CGroups::loadCurrent()->_guid;
}

$crc = CBcbProduit::getHash($crc);

// Chargement du produit
$produit_livret = new CBcbProduitLivretTherapeutique();
$produit_livret->load($code_cip, $crc);
$produit_livret->loadRefProduit();

$produit_livret->updateFormFields();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("code_cip", $code_cip);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("lettre", $lettre);
$smarty->assign("produit_livret", $produit_livret);

if (isset($function_guid)) {
  $smarty->assign("function_guid", $function_guid);
}

$smarty->display("edit_produit_livret.tpl");

?>