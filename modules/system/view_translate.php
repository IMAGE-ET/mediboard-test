<?php /* SYSTEM $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author Sébastien Fillonneau
 */

global $AppUI, $canRead, $canEdit, $m;

// only user_type of Administrator (1) can access this page
if (!$canEdit || $AppUI->user_type != 1) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$module = mbGetValueFromGetOrSession("module" , "admin");
// liste des dossiers modules + common et styles
$modules = arrayMerge( array("common"=>"common", "styles"=>"styles") ,$AppUI->readDirs("modules"));
mbRemoveValuesInArray(".svn", $modules);
ksort($modules);

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn",$localesDirs);

// Récupération du fichier demandé pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "locales/$locale/$modules[$module].php";
  $translateModule->load();
  $contenu_file[$locale] = $translateModule->values;
}

// Réattribution des clés et organisation
$trans = array();
foreach($localesDirs as $locale){
	foreach($contenu_file[$locale] as $k=>$v){
		$trans[ (is_int($k) ? $v : $k) ][$locale] = $v;
	}
}

// Remplissage par null si la valeur n'existe pas
foreach($trans as $k=>$v){
  foreach($localesDirs as $keyLocale=>$valueLocale){
  	if(!isset($trans[$k][$keyLocale])){
  		$trans[$k][$keyLocale] = null;
  	}
  }
}
uksort($trans,"strnatcasecmp");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("locales"  , $localesDirs);
$smarty->assign("modules"  , $modules);
$smarty->assign("module"   , $module);
$smarty->assign("trans"    , $trans);

$smarty->display("view_translate.tpl");
?>