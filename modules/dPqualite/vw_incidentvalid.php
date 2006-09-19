<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$fiche_ei_id = mbGetValueFromGetOrSession("fiche_ei_id",null);
$catFiche = array();

$fiche = new CFicheEi;
if(!$fiche->load($fiche_ei_id)){
  // Cette fiche n'est pas valide
  $fiche_ei_id = null;
  mbSetValueToSession("fiche_ei_id");
  $fiche = new CFicheEi;
}else{
  $fiche->loadRefsFwd();
  $fiche->loadRefItems();
  
  // Liste des Catégories d'EI
  $listCategories = new CEiCategorie;
  $listCategories = $listCategories->loadList(null, "nom");
  //  
  foreach($listCategories as $keyCat=>$valueCat){
    $cattrouvee = null;
    
    foreach($fiche->_ref_items as $keyItem=>$valueItem){
      if($fiche->_ref_items[$keyItem]->ei_categorie_id==$keyCat){
        if(!$cattrouvee){
          $catFiche[$listCategories[$keyCat]->nom] = array();
        }
        $catFiche[$listCategories[$keyCat]->nom][] = $fiche->_ref_items[$keyItem];
        $cattrouvee = 1;
      }
    }
  }

}


// Liste des Fiches Traitées
$listFichesTermine = new CFicheEi;
$where = "`valid_user_id` IS NOT NULL";
$order = "date_incident DESC";
$listFichesTermine = $listFichesTermine->loadlist($where,$order);
foreach($listFichesTermine as $key=>$value){
  $listFichesTermine[$key]->loadRefsFwd();
}

// Liste des Fiches A Traiter
$listFichesEnCours = new CFicheEi;
$where = "`valid_user_id` IS NULL";
$order = "date_incident DESC";
$listFichesEnCours = $listFichesEnCours->loadlist($where,$order);
foreach($listFichesEnCours as $key=>$value){
  $listFichesEnCours[$key]->loadRefsFwd();
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("catFiche"          , $catFiche);
$smarty->assign("user_id"           , $AppUI->user_id);
$smarty->assign("fiche"             , $fiche);
$smarty->assign("listFichesTermine" , $listFichesTermine);
$smarty->assign("listFichesEnCours" , $listFichesEnCours);

$smarty->display("vw_incidentvalid.tpl");
?>
