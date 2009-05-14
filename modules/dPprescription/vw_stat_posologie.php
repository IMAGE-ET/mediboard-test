<?php /* $Id: vw_edit_protocole.php 6138 2009-04-21 13:50:16Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$code_cip = mbGetValueFromGet("code_cip");
$praticien_id = mbGetValueFromGet("praticien_id");

$praticien = new CMediusers();
$praticien->load($praticien_id);

$produit = new CBcbProduit();
$produit->load($code_cip);

// Initialisation
$line = new CPrescriptionLineMedicament();
$prescription = new CPrescription();

foreach($prescription->_specs['type']->_list as $_presc){
  // Chargement des poso pour le praticien
  if($praticien->_id){
	  $line->loadMostUsedPoso($produit->code_cis, $praticien_id, $_presc);
	  $stats["praticien"][$_presc] = $line->_most_used_poso;
  }
  // Chargement des poso global de l'etalissement
	$line->loadMostUsedPoso($produit->code_cis, "global", $_presc);
	$stats["global"][$_presc] = $line->_most_used_poso;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("stats", $stats);
$smarty->assign("praticien", $praticien);
$smarty->assign("prescription", $prescription);
$smarty->assign("produit", $produit);
$smarty->display("vw_stat_posologie.tpl");

?>