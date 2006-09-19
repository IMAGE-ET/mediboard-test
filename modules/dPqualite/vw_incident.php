<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/


global $AppUI, $canRead, $canEdit, $m, $g;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$fiche = new CFicheEi;


// Liste des Catégories
$firstdiv = null;
$listCategories = new CEiCategorie;
$listCategories = $listCategories->loadList(null, "nom");
foreach ($listCategories as $key=>$value){
  if($firstdiv===null){
    $firstdiv = $key;
  }
  $listCategories[$key]->loadRefsBack();
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
$smarty = new CSmartyDP(1);

$smarty->assign("datenow"        , mbDate());
$smarty->assign("heures"         , $heures);
$smarty->assign("mins"           , $mins);
$smarty->assign("fiche"          , $fiche);
$smarty->assign("firstdiv"       , $firstdiv);
$smarty->assign("user_id"        , $AppUI->user_id);
$smarty->assign("listCategories" , $listCategories);

$smarty->display("vw_incident.tpl");
?>