<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$fiche_ei_id = mbGetValueFromGet("fiche_ei_id",0);

$fiche  = new CFicheEi;
$aUsers = array();
$listFct = new CFunctions();

if($can->admin && $fiche_ei_id){
  // Droit admin et edition de fiche
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
  
// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP();

$smarty->assign("datenow"        , mbDate());
$smarty->assign("heurenow"       , mbTranformTime(null,null,"%H"));
$smarty->assign("minnow"         , mbTranformTime(null,null,"%M"));
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