<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$composant = mbGetValueFromGet("composant", "");
$code = mbGetValueFromGet("code", "");
$libelle = mbGetValueFromGet("libelle", "");

$rechercheLivretComposant = mbGetValueFromGet("rechercheLivretComposant");
$rechercheLivretComposant = ($rechercheLivretComposant == "true") ? "1" : "0";

// Chargement des compositions qui contienne le composant recherche
$composition = new CBcbComposition();

if($composant){
  $composition->searchComposant($composant);
}

if($code){
  $composition->searchProduits($code, $rechercheLivretComposant);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rechercheLivretComposant", $rechercheLivretComposant);
$smarty->assign("libelle", $libelle);
$smarty->assign("code", $code);
$smarty->assign("composant", $composant);
$smarty->assign("composition", $composition);

$smarty->display("inc_vw_composants.tpl");


?>