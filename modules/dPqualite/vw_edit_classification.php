<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $canRead, $canEdit, $canAdmin, $m;

if (!$canAdmin) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$typeVue = mbGetValueFromGetOrSession("typeVue", 0);

require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP();

$smarty->assign("typeVue"  , $typeVue  );

if($typeVue){
  // Liste des Themes
  $doc_theme_id = mbGetValueFromGetOrSession("doc_theme_id", null);
  // Chargement du theme demandé
  $theme=new CThemeDoc;
  $theme->load($doc_theme_id);
  // Liste des Themes
  $listThemes = new CThemeDoc;
  $listThemes = $listThemes->loadList();
  
  // Création du Template
  $smarty->assign("theme"      , $theme      );
  $smarty->assign("listThemes" , $listThemes);
  $smarty->display("vw_edit_themes.tpl");
}else{
  // Liste des Chapitres
  $doc_chapitre_id = mbGetValueFromGetOrSession("doc_chapitre_id", null);
  // Chargement du chapitre demandé
  $chapitre=new CChapitreDoc;
  $chapitre->load($doc_chapitre_id);
  // Liste des Chapitres
  $listChapitres = new CChapitreDoc;
  $listChapitres = $listChapitres->loadList();
  
  // Création du Template
  $smarty->assign("chapitre"      , $chapitre      );
  $smarty->assign("listChapitres" , $listChapitres);
  $smarty->display("vw_edit_chapitres.tpl"); 
}
?>