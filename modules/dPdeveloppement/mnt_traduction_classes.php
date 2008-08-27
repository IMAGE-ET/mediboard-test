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

$module = mbGetValueFromGetOrSession("module" , "system");
$classes = CModule::getClassesFor($module);
$language = mbGetValueFromGetOrSession("language",'fr');

// Hack to have CModule in system locale file
if ($module == "system") {
  $classes[] = "CModule";
}

// liste des dossiers modules + common et styles
$modules = array_keys(CModule::getInstalled());
sort($modules);

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
CMbArray::removeValue(".svn",$localesDirs);
//CMbArray::removeValue("en",$localesDirs);

// Récupération du fichier demandé pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "locales/$locale/$module.php";
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

global $items, $completions;
$items = array();
$completions = array();

// Ajoute un item de localisation
function addLocale($class, $cat, $name, $language) {
  global $trans, $items, $completions;
  $items[$class][$cat][$name] = array_key_exists($name, $trans) ? @$trans[$name][$language] : "";
  
  // Stats
  @$completions[$class]["total"]++;
  if ($items[$class][$cat][$name]) {
    @$completions[$class]["count"]++;
  }
  
  @$completions[$class]["percent"] = round(100 * $completions[$class]["count"] / $completions[$class]["total"]);
}

// Parcours des classes
foreach ($classes as $class) {
  $object = new $class;
  $ref_modules = $object->_specs;
  $classname = $object->_class_name;
  
  // Traductions au niveau classe
  addLocale($classname, $classname, "$classname", $language);
  addLocale($classname, $classname, "$classname.none", $language);
  addLocale($classname, $classname, "$classname.one", $language);
  addLocale($classname, $classname, "$classname.all", $language);
  addLocale($classname, $classname, "$classname.select", $language);
  addLocale($classname, $classname, "$classname-msg-create", $language);
  addLocale($classname, $classname, "$classname-msg-modify", $language);
  addLocale($classname, $classname, "$classname-msg-delete", $language);
  addLocale($classname, $classname, "$classname-title-create", $language);
  addLocale($classname, $classname, "$classname-title-modify", $language);
  
  // Traductions pour la clé 
  $prop = $object->_spec->key;
  addLocale($classname, $prop, "$classname-$prop", $language);
  addLocale($classname, $prop, "$classname-$prop-desc", $language);
  addLocale($classname, $prop, "$classname-$prop-court", $language);
  
  // Traductions de chaque propriété
	foreach ($object->_specs as $prop => $spec) { 
    if (!$spec->prop) {
      continue;
    }
    
    if (in_array($prop, array($object->_spec->key, "_view", "_shortview"))) {
      continue;
    }
    
	  addLocale($classname, $prop, "$classname-$prop", $language);
	  addLocale($classname, $prop, "$classname-$prop-desc", $language);
	  addLocale($classname, $prop, "$classname-$prop-court", $language);
  
    if ($spec instanceof CEnumSpec) {
      if (!$spec->notNull) {
	      addLocale($classname, $prop, "$classname.$prop.", $language);
      }
      
      foreach (explode("|", $spec->list) as $value) {
	      addLocale($classname, $prop, "$classname.$prop.$value", $language);
      }
    }
    
    if ($spec instanceof CRefSpec) {
      // CAccessLog serves as dummy class when we need to instanciate anyhow
      $fwdClass = ($spec->class != "CMbObject") ? $spec->class : "CAccessLog";//&& has_default_constructor($spec->class) ? $spec->class : "CAccessLog";
      $fwdObject = new $fwdClass;
      
      // Find corresponding back name
      $backName = array_search("$spec->className $spec->fieldName", $fwdObject->_backRefs);
	    addLocale($classname, $prop, "$spec->class-back-$backName", $language);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("items"  		, $items);
$smarty->assign("completions" , $completions);
$smarty->assign("locales"  		, $localesDirs);
$smarty->assign("modules"  		, $modules);
$smarty->assign("module"   		, $module);
$smarty->assign("trans"    		, $trans);
$smarty->assign("language"    		, $language);

$smarty->display("mnt_traduction_classes.tpl");
?>