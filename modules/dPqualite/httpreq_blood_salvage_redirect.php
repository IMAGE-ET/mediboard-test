<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Alexandre Germonneau
*/


global $AppUI, $can, $m, $g;

$can->needsRead();

$type_ei_id = mbGetValueFromGetOrSession("type_ei_id");
$blood_salvage_id = mbGetValueFromGetOrSession("blood_salvage_id");

$blood_salvage = new CBloodSalvage();
$type_fiche = new CTypeEi();
$fiche_ei_id = 0 ;

$fiche  = new CFicheEi;
$aUsers = array();
$listFct = new CFunctions();

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

if($type_ei_id) {
  $type_fiche->load($type_ei_id);
  $fiche->elem_concerne = $type_fiche->concerne;
  $fiche->descr_faits = $type_fiche->desc;
  $fiche->evenements= "124";
  if($blood_salvage_id) {
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
$firstdiv = 25;
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
        $listCategories[$key]->checked .= "|". $keyItem;
      }else{
        $listCategories[$key]->checked = $keyItem;
      }
    }else{
      $listCategories[$key]->_ref_items[$keyItem]->checked = false;
    }
  }
}
// Liste minutes
$mins = array();
for ($i = 0; $i < 60; $i++) {
  $mins[] = $i;
}
// Liste heures
$heures = array();
for ($i = 0; $i <= 23; $i++) {
  $heures[] = $i;
}
$smarty = new CSmartyDP();


$smarty->assign("datenow"        , mbDate());
$smarty->assign("heurenow"       , mbTransformTime(null,null,"%H"));
$smarty->assign("minnow"         , mbTransformTime(null,null,"%M"));
$smarty->assign("heures"         , $heures);
$smarty->assign("mins"           , $mins);
$smarty->assign("fiche"          , $fiche);
$smarty->assign("firstdiv"       , $firstdiv);
$smarty->assign("user_id"        , $AppUI->user_id);
$smarty->assign("listCategories" , $listCategories);
$smarty->assign("aUsers"         , $aUsers);
$smarty->assign("listFct"        , $listFct);
$smarty->display("vw_incident.tpl");
?>