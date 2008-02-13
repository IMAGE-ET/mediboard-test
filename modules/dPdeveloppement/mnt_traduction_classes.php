<?php

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision:  $
 * @author Poiron Yohann
 */

global $AppUI, $can, $m;

// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = mbGetValueFromGetOrSession("module" , "admin");

$classes = mbGetClassByModule($module);

// liste des dossiers modules + common et styles
$modules = $AppUI->readDirs("modules");
CMbArray::removeValue(".svn", $modules);
ksort($modules);

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
CMbArray::removeValue(".svn",$localesDirs);
CMbArray::removeValue("en",$localesDirs);

// Récupération du fichier demandé pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "locales/fr/$modules[$module].php";
  $translateModule->load();
  $contenu_file[$locale] = $translateModule->values;
}

// Réattribution des clés et organisation
global $trans;
$trans = array();
foreach($localesDirs as $locale){
	foreach($contenu_file[$locale] as $k=>$v){
		$trans[ (is_int($k) ? $v : $k) ][$locale] = $v;
	}
}

function checkTrans(&$array, $key) {
  global $trans;
  $array[$key] = array_key_exists($key,$trans) ? $trans[$key]["fr"] : "";
}


$backSpecs = array();
$backRefs = array();
foreach($classes as $class) {
  $object = new $class;
  $ref_modules = $object->_specs;
  $classname = $object->_class_name;
  
  // Traductions au niveau classe
  checkTrans($backSpecs[$classname][$classname], "$classname");
  checkTrans($backSpecs[$classname][$classname], "$classname.one");
  checkTrans($backSpecs[$classname][$classname], "$classname.more");
  checkTrans($backSpecs[$classname][$classname], "$classname.none");
    
  // Traductions pour la clé 
  $prop = $object->_tbl_key;
  checkTrans($backSpecs[$classname][$prop], "$classname-$prop");
  checkTrans($backSpecs[$classname][$prop], "$classname-$prop-desc");
  checkTrans($backSpecs[$classname][$prop], "$classname-$prop-court");
  
  // Traductions de chaque propriété
	foreach ($object->_specs as $prop => $spec) { 
    if (!$spec->prop) {
      continue;
    }
    checkTrans($backSpecs[$classname][$prop], "$classname-$prop");
    checkTrans($backSpecs[$classname][$prop], "$classname-$prop-desc");
    checkTrans($backSpecs[$classname][$prop], "$classname-$prop-court");
    
    if ($spec instanceof CEnumSpec) {
      if (!$spec->notNull) {
	      checkTrans($backSpecs[$classname][$prop], "$classname.$prop.");        
      }
      
      foreach (explode("|", $spec->list) as $value) {
	      checkTrans($backSpecs[$classname][$prop], "$classname.$prop.$value");
      }
    }
    
    if ($spec instanceof CRefSpec) {
      // CAccessLog serves as dummy class when we need to instanciate anyhow
      $fwdClass = ($spec->class != "CMbObject") && has_default_constructor($spec->class) ? $spec->class : "CAccessLog";
      $fwdObject = new $fwdClass;
      
      // Find corresponding back name
      $backName = array_search("$spec->className $spec->fieldName", $fwdObject->_backRefs);
      checkTrans($backSpecs[$classname][$prop], "$spec->class-back-$backName");
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("locales"  		, $localesDirs);
$smarty->assign("modules"  		, $modules);
$smarty->assign("module"   		, $module);
$smarty->assign("trans"    		, $trans);
$smarty->assign("backSpecs"   , $backSpecs);

$smarty->display("mnt_traduction_classes.tpl");
?>