<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien Ménager
 */

global $can;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');

$destockages = array();
$lines = array();

// Chargement de toutes les administrations dans la periode donnee
$administration = new CAdministration();
$where = array();
$where["dateTime"] = "BETWEEN '$date_min' AND '$date_max'"; 
$where["object_class"] = " = 'CPrescriptionLineMedicament'";
$administrations = $administration->loadList($where);

// Calcul des quantités administrées
foreach($administrations as $_administration){
  $_administration->loadTargetObject();	
	$line =& $_administration->_ref_object;
  $code_cip = $line->code_cip;
	if(!isset($destockages[$code_cip]["quantite"])) {
		$destockages[$code_cip]["quantite"] = 0;
	}
  $destockages[$code_cip]["quantite"] += $_administration->quantite;
  // Tableaux de cache de medicaments
  if(!isset($medicaments[$code_cip])){
  	$line->_ref_produit->loadConditionnement();
    $medicaments[$code_cip] =& $line->_ref_produit;
  }
}

// Calcul du nombre de boites correspondant aux administrations
foreach($destockages as $code_cip => $_destockage){
  $medicament = $medicaments[$code_cip];
  $presentation = $destockages[$code_cip]["quantite"]/$medicament->nb_unite_presentation/$medicament->nb_presentation;
  if (!isset($destockages[$code_cip]["nb_produit"])){
    $destockages[$code_cip]["nb_produit"] = 0;
  }
  $destockages[$code_cip]["nb_produit"] = $presentation; 
}


// On arrondit la quantite de "boites"
foreach($destockages as &$produit){
  if(strstr($produit["nb_produit"], '.')){
    $produit["nb_produit"] = ceil($produit["nb_produit"]);
  }
}  

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('destockages', $destockages);
$smarty->display('inc_destockages_service_list.tpl');

?>