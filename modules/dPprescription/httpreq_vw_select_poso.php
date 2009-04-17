<?php /* $Id: r $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$code_cip = mbGetValueFromGet("code_cip");

$produit = new CBcbProduit();
if($code_cip && is_numeric($code_cip)){
  $produit->load($code_cip);
} else {
  return;
}

// Construction du tableau de prises disponibles
$unites_prise = array();
if($code_cip){
	if ($produit->libelle_presentation){
	  $unites_prise[] = $produit->libelle_presentation;
	}
	
	foreach($produit->_ref_posologies as $_poso){
	  $unite = $_poso->_code_unite_prise["LIBELLE_UNITE_DE_PRISE_PLURIEL"];
	  if($_poso->p_kg) {
	    $unites_prise[] = "$unite/kg";
	  }
		$unites_prise[] = $unite;
	}
	
	if (is_array($unites_prise)){
	  $unites_prise = array_unique($unites_prise);
}
}

$prise = new CPrisePosologie();
$prise->quantite = 1;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prise", $prise);
$smarty->assign("unites_prise", $unites_prise);
$smarty->display("../../dPprescription/templates/inc_vw_select_poso_lite.tpl");

?>