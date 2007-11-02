<?php /* SYSTEM $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author S�bastien Fillonneau
 */

global $AppUI, $can, $m;

// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = mbGetValueFromGetOrSession("module" , "admin");

// liste des dossiers modules + common et styles
$modules = array_merge( array("common"=>"common", "styles"=>"styles") ,$AppUI->readDirs("modules"));
CMbArray::removeValue(".svn", $modules);
ksort($modules);

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
CMbArray::removeValue(".svn",$localesDirs);

// R�cup�ration du fichier demand� pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "locales/$locale/$modules[$module].php";
  $translateModule->load();
  $contenu_file[$locale] = $translateModule->values;
}

// R�attribution des cl�s et organisation
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

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("locales"  , $localesDirs);
$smarty->assign("modules"  , $modules);
$smarty->assign("module"   , $module);
$smarty->assign("trans"    , $trans);

$smarty->display("view_translate.tpl");
?>