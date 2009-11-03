<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $can, $m;
  
$praticien_id = CValue::getOrSession("praticien_id");
$prat = new CMediusers();
$prat->load($praticien_id);


$categorie = new CConsultationCategorie();
$whereCategorie["function_id"] = " = '$prat->function_id'";
$orderCategorie = "nom_categorie ASC";
$categories = $categorie->loadList($whereCategorie,$orderCategorie);


// Creation du tableau de categories simplifié pour le traitement en JSON
$listCat = array();
foreach($categories as $key => $cat){
  $listCat[$cat->_id] = $cat->nom_icone;
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories", $categories);
$smarty->assign("listCat", $listCat);
$smarty->assign("categorie_id", "");


$smarty->display("httpreq_view_list_categorie.tpl");

?>