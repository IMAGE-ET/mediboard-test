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

$owner_crc = $function_guid ? $function_guid : CGroups::loadCurrent()->_guid;
$owner_crc = CBcbProduit::getHash($owner_crc);

// Chargement du produit dans le livret therapeutique
$produit_livret = CProduitLivretTherapeutique::getProduit($owner_crc, $code_cip);
$produit_livret->loadRefProduit();

// Chargement des unites de prises possibles pour le produit
$line_med = new CPrescriptionLineMedicament();
$line_med->code_cip = $code_cip;
$line_med->loadRefProduit();
$line_med->loadRefsFwd();
$produit_livret->_unites_prise = $line_med->_unites_prise;

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