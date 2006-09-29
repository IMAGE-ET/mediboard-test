<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $canAdmin, $m, $g;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$fiche_ei_id = mbGetValueFromGetOrSession("fiche_ei_id",null);
$ficheAnnuleVisible = mbGetValueFromGetOrSession("ficheAnnuleVisible" , 0);
$ficheTermineVisible = mbGetValueFromGetOrSession("ficheTermineVisible" , 0);
$catFiche = array();

$fiche = new CFicheEi;
if(!$fiche->load($fiche_ei_id) || (!$canAdmin && $fiche->service_valid_user_id!=$AppUI->user_id)){
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

// Liste des Fiches en Attente de Traitement

$listFichesAttente = new CFicheEi;
$where = array();
$where["annulee"] = "= '0'";
if(!$canAdmin){
  $where["service_date_validation"] = " IS NULL";
  $where["service_valid_user_id"] = "= '$AppUI->user_id'";
}else{
  $where["date_validation"] = " IS NULL";
}
$order = "date_incident DESC";
$listFichesAttente = $listFichesAttente->loadlist($where,$order);
foreach($listFichesAttente as $key=>$value){
  $listFichesAttente[$key]->loadRefsFwd();
}


// Liste des Fiches En cours de traitement
$listFichesEnCours = new CFicheEi;
$where = array();
$where["annulee"]               = "= '0'";
$where["qualite_date_controle"] = "IS NULL";
if(!$canAdmin){
  $where["service_date_validation"] = " IS NOT NULL";
  $where["service_valid_user_id"] = "= '$AppUI->user_id'";
}else{
  $where["date_validation"] = "IS NOT NULL";
}
$order = "date_incident DESC";
$listFichesEnCours = $listFichesEnCours->loadlist($where,$order);
foreach($listFichesEnCours as $key=>$value){
  $listFichesEnCours[$key]->loadRefsFwd();
}

//Liste des Fiches Annulées
$listFichesAnnulees = new CFicheEi;
$where = array();
$where["annulee"] = "= '1'";
$order = "date_incident DESC";
$listFichesAnnulees = $listFichesAnnulees->loadlist($where,$order);
foreach($listFichesAnnulees as $key=>$value){
  $listFichesAnnulees[$key]->loadRefsFwd();
}
    
// Liste des Fiches Terminée
$listFichesTermine = new CFicheEi;
$where = array();
$where["annulee"]               = "= '0'";
$where["qualite_date_controle"] = "IS NOT NULL";
if(!$canAdmin){
  $where["service_valid_user_id"] = "= '$AppUI->user_id'";
}
$order = "date_incident DESC";
$listFichesTermine = $listFichesTermine->loadlist($where,$order);
foreach($listFichesTermine as $key=>$value){
  $listFichesTermine[$key]->loadRefsFwd();
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("user_id"   , $AppUI->user_id);
$smarty->assign("catFiche"  , $catFiche);  
$smarty->assign("fiche"     , $fiche);
$smarty->assign("listFichesTermine" , $listFichesTermine);
$smarty->assign("listFichesEnCours" , $listFichesEnCours); 
$smarty->assign("listFichesAttente" , $listFichesAttente);
$smarty->assign("ficheAnnuleVisible", $ficheAnnuleVisible);
$smarty->assign("listFichesAnnulees", $listFichesAnnulees);
$smarty->assign("ficheTermineVisible", $ficheTermineVisible);
$smarty->assign("today"             , mbDate());

if($canAdmin){ 
  // Chargement de la liste des Chef de services / utilisateur
  $module = CModule::getInstalled("dPqualite");
  $permUserEdit = new CPermModule;
  $listUsersEdit = new CMediusers;
  $listUsersEdit = $listUsersEdit->loadUsers(PERM_READ, null, null);
  foreach($listUsersEdit as $keyUser=>$infoUser){
    if(!$permUserEdit->getInfoModule("permission", $module->mod_id, PERM_EDIT, $keyUser)){
      unset($listUsersEdit[$keyUser]);
    }
  }

  $smarty->assign("listUsersEdit" , $listUsersEdit);
  
}
$smarty->display("vw_incidentvalid.tpl");
?>