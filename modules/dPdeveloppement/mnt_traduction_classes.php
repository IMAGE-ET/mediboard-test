<?php

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author Poiron Yohann
 */

global $AppUI, $can, $m;


// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = CValue::getOrSession("module" , "system");
$classes = CModule::getClassesFor($module);
global $language;
$language = CValue::getOrSession("language",'fr');

// Hack to have CModule in system locale file
if ($module == "system") {
  $classes[] = "CModule";
}

// If the locale files are in the module's "locales" directory
$in_module = (is_dir("modules/$module/locales"));

// liste des dossiers modules + common et styles
$modules = array_keys(CModule::getInstalled());
sort($modules);

// Dossier des traductions
$localesDirs = array();
if ($in_module) {
  $files = glob("modules/$module/locales/*");
  foreach ($files as $file) {
    $name = basename($file, ".php");
    $localesDirs[$name] = $name;
  }
}
else {
  $localesDirs = $AppUI->readDirs("locales");
  CMbArray::removeValue(".svn",$localesDirs);
}

// Récupération du fichier demandé pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = ($in_module ? "modules/$module/locales/$locale.php" : "locales/$locale/$module.php");
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
function addLocale($class, $cat, $name) {
  global $trans, $items, $completions, $language;
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
  addLocale($classname, $classname, "$classname");
  addLocale($classname, $classname, "$classname.none");
  addLocale($classname, $classname, "$classname.one");
  addLocale($classname, $classname, "$classname.all");
  //addLocale($classname, $classname, "$classname.select");
  addLocale($classname, $classname, "$classname-msg-create");
  addLocale($classname, $classname, "$classname-msg-modify");
  addLocale($classname, $classname, "$classname-msg-delete");
  addLocale($classname, $classname, "$classname-title-create");
  addLocale($classname, $classname, "$classname-title-modify");
  
  // Traductions pour la clé 
  $prop = $object->_spec->key;
  addLocale($classname, $prop, "$classname-$prop");
  addLocale($classname, $prop, "$classname-$prop-desc");
  addLocale($classname, $prop, "$classname-$prop-court");
  
  // Traductions de chaque propriété
	foreach ($object->_specs as $prop => $spec) { 
    if (!$spec->prop) {
      continue;
    }
    
    if (in_array($prop, array($object->_spec->key, "_view", "_shortview"))) {
      continue;
    }
    
    // Ajout des _ dans la maintenance traduction
    //if ($prop[0] == "_") {
    //  continue;
    //}
    
	  addLocale($classname, $prop, "$classname-$prop");
	  addLocale($classname, $prop, "$classname-$prop-desc");
	  addLocale($classname, $prop, "$classname-$prop-court");
  
    if ($spec instanceof CEnumSpec) {
      if (!$spec->notNull) {
	      addLocale($classname, $prop, "$classname.$prop.");
      }
      
      foreach (explode("|", $spec->list) as $value) {
	      addLocale($classname, $prop, "$classname.$prop.$value");
      }
    }
    
    if ($spec instanceof CRefSpec) {
      $fwdClass = $spec->class;
      $fwdObject = new $fwdClass;
      
      // Find corresponding back name
      $backName = array_search("$spec->className $spec->fieldName", $fwdObject->_backProps);
	    addLocale($classname, $prop, "$spec->class-back-$backName");
    }
  }
  
  // Traductions pour les uniques
  foreach (array_keys($object->_spec->uniques) as $unique) {
    addLocale($classname, "Failures", "$classname-failed-$unique");
  }
}

// Parcours des variables de configuration
function addConfigConfigCategory($chapter, $category, $values) {
  $prefix = $chapter ? "$chapter-$category" : $category;
  
  if (!is_array($values)) {
    addLocale("Config", "global", "config-$prefix");
    addLocale("Config", "global", "config-$prefix-desc");
    return;
  }
  
  foreach ($values as $key => $value) {
    addLocale("Config", $category, "config-$prefix-$key");
    addLocale("Config", $category, "config-$prefix-$key-desc");
    
  }
}

if ($categories = @CAppUI::conf($module)) {
	foreach ($categories as $category => $values) {
	  addConfigConfigCategory($module, $category, $values);
	}
}

if ($module == "system") {
  foreach (CAppUI::conf() as $chapter => $values) {
    if (!CModule::exists($chapter) && $chapter != "db") {
      addConfigConfigCategory(null, $chapter, $values);
    }
  }
}

// Ajout du module et des onglets
CAppUI::requireModuleFile($module, "index");
addLocale("Module", "Module", "module-$module-court");
addLocale("Module", "Module", "module-$module-long");
if (!empty(CModule::getInstalled($module)->_tabs)) {
  foreach (CModule::getInstalled($module)->_tabs as $_tab) {
    addLocale("Module", "Tabs", "mod-$module-tab-" . $_tab[0]);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("items"  	    , $items);
$smarty->assign("completions" , $completions);
$smarty->assign("locales"  		, $localesDirs);
$smarty->assign("modules"  		, $modules);
$smarty->assign("module"   		, $module);
$smarty->assign("trans"    		, $trans);
$smarty->assign("language"    , $language);

$smarty->display("mnt_traduction_classes.tpl");
?>