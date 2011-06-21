<?php /* $Id: vw_edit_protocole.php 6138 2009-04-21 13:50:16Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$filter = CValue::get("filter");
$object_class = CValue::get("object_class");
$praticien_id = CValue::get("praticien_id");

$praticien = new CMediusers();
$praticien->load($praticien_id);

$line = new $object_class;

$produit = new CBcbProduit();
$element_prescription = new CElementPrescription();
  
if($line instanceof CPrescriptionLineMedicament){
	$produit->load($filter);
	$filter = $produit->code_cis;	
} else {
  $element_prescription->load($filter);	
}

// Initialisation
$prescription = new CPrescription();

foreach($prescription->_specs['type']->_list as $type_prescription){
  // Chargement des poso pour le praticien
  if($praticien->_id){
	  $line->loadMostUsedPoso($filter, $praticien_id, $type_prescription);
	  $stats["praticien"][$type_prescription] = $line->_most_used_poso;
  }
  // Chargement des poso global de l'etalissement
	$line->loadMostUsedPoso($filter, "global", $type_prescription);
	$stats["global"][$type_prescription] = $line->_most_used_poso;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("stats", $stats);
$smarty->assign("praticien", $praticien);
$smarty->assign("prescription", $prescription);
$smarty->assign("produit", $produit);
$smarty->assign("element_prescription", $element_prescription);
$smarty->assign("line", $line);
$smarty->display("vw_stat_posologie.tpl");

?>