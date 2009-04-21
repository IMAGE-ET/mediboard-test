<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$fiche_ei_id = mbGetValueFromGet("fiche_ei_id",0);

//Récupération du type de fiche à générer et de la RSPO concernée.
$type_ei_id = mbGetValueFromGet("type_ei_id");
$blood_salvage_id = mbGetValueFromGet("blood_salvage_id");

$fiche  = new CFicheEi();
$listFct = new CFunctions();

// Droit admin et edition de fiche
if($can->admin && $fiche_ei_id){
  $fiche->load($fiche_ei_id);
}

// Chargement des Utilisateurs
if($can->admin) {
  $listFct = CMediusers::loadFonctions(PERM_READ);
  foreach($listFct as &$fct) {
    $fct->loadRefsBack();
  }
}

$fiche->loadRefsFwd();
if(!$fiche->_ref_evenement){
  $fiche->_ref_evenement = array();
}

/*
 * Si l'on est dans le cas où nous souhaitons préremplir automatiquement 
 * quelques champs à l'aide du modèle de fiche d'incident (module cell saver).
 */
if($type_ei_id) {
	$type_fiche = new CTypeEi();
  $type_fiche->load($type_ei_id);
  $fiche->elem_concerne = $type_fiche->concerne;
  $fiche->descr_faits = $type_fiche->desc;
  $fiche->evenements = $type_fiche->evenements;
  $fiche->type_incident = $type_fiche->type_signalement;
  $fiche->_ref_evenement  =  $type_fiche->_ref_evenement;
  
  if($blood_salvage_id) {
  	$blood_salvage = new CBloodSalvage();
    $blood_salvage->load($blood_salvage_id);
    $blood_salvage->loadRefsFwd();
    
    if($fiche->elem_concerne == "pat") {
      $fiche->elem_concerne_detail = $blood_salvage->_ref_patient->_view;
    }
    if($fiche->elem_concerne == "mat") {
      $fiche->elem_concerne_detail = $blood_salvage->_ref_cell_saver->_view;
    }
  }
}

// Liste des Catégories
$firstdiv = null;

$listCategories = new CEiCategorie;
$listCategories = $listCategories->loadList(null, "nom");
foreach ($listCategories as $key=>$value){
  if($firstdiv===null){
    $firstdiv = $key;
  }
  $listCategories[$key]->loadRefsBack();
  $listCategories[$key]->checked = null;
  foreach($listCategories[$key]->_ref_items as $keyItem=>$valueItem){
    if(in_array($keyItem,$fiche->_ref_evenement)){
      $listCategories[$key]->_ref_items[$keyItem]->checked = true;
      if($listCategories[$key]->checked){
        $listCategories[$key]->checked .= "|$keyItem";
      }else{
        $listCategories[$key]->checked = $keyItem;
      }
    }else{
    	$listCategories[$key]->_ref_items[$keyItem]->checked = false;
    }
  }
}

if (!$fiche->date_incident) {
	$fiche->date_incident = mbDateTime();
}
$fiche->updateFormFields();
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("fiche"          , $fiche);
$smarty->assign("firstdiv"       , $firstdiv);
$smarty->assign("listCategories" , $listCategories);
$smarty->assign("listFct"        , $listFct);

$smarty->display("vw_incident.tpl");
?>