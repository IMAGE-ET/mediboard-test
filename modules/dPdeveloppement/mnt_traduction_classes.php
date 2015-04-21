<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

global $language;

$module = CValue::getOrSession("module", "system");
$language = CValue::getOrSession("language", "fr");

if ($module != "common") {
  $classes = CModule::getClassesFor($module);

  // Hack to have CModule in system locale file
  if ($module == "system") {
    $classes[] = "CModule";
  }
}
else {
  $classes = array();
}

// liste des dossiers modules + common et styles
$modules = array_keys(CModule::getInstalled());
$modules[] = "common";
sort($modules);

// Dossier des traductions
$localesDirs = array();

if ($module != "common") {
  $files = glob("modules/$module/locales/*");

  foreach ($files as $file) {
    $name = basename($file, ".php");
    $localesDirs[$name] = $file;
  }
}
else {
  $files = glob("locales/*/common.php");
  foreach ($files as $file) {
    $name = basename(dirname($file));
    $localesDirs[$name] = $file;
  }
}

// Récupération du fichier demandé pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach ($localesDirs as $locale => $path) {
  $translateModule->options = array("name" => "locales");
  //$translateModule->sourcePath = $path;
  $translateModule->targetPath = $path;
  $translateModule->load();
  $contenu_file[$locale] = $translateModule->values;
}

// Réattribution des clés et organisation
global $trans;
$trans = array();
foreach ($localesDirs as $locale => $path) {
  foreach ($contenu_file[$locale] as $k=>$v) {
    $trans[ (is_int($k) ? $v : $k) ][$locale] = $v;
  }
}


global $items, $completions, $all_locales, $total_count, $local_count;
$items = array();
$completions = array();
$all_locales = $contenu_file[$language];
$total_count = 0;
$local_count = 0;

/**
 * Add a locale item in a three levels collection
 * (Yet more of an internationalisation item)
 *
 * @param string $class Class name
 * @param string $cat   Category name
 * @param string $name  Item name
 *
 * @return void
 */
function addLocale($class, $cat, $name) {
  global $trans, $items, $completions, $language, $all_locales, $total_count, $local_count;
  
  $items[$class][$cat][$name] = array_key_exists($name, $trans) ? @$trans[$name][$language] : "";
  $items[$class][$cat][$name] = str_replace(array('\n', '\t'), array("\n", "\t"), $items[$class][$cat][$name]);
  
  unset($all_locales[$name]);
  
  // Stats
  @$completions[$class]["total"]++;
  $total_count++;
  if ($items[$class][$cat][$name]) {
    @$completions[$class]["count"]++;
    $local_count++;
  }

  @$completions[$class]["percent"] = round(100 * $completions[$class]["count"] / $completions[$class]["total"]);
}

$archives = array();

// Parcours des classes
foreach ($classes as $class) {
  /** @var CModelObject $object */
  $object = new $class;
  $classname = $object->_class;
  
  // Traductions au niveau classe
  addLocale($classname, $classname, "$classname");
  if ($object->_spec->archive) {
    $archives[$class] = true;
    continue;
  }

  addLocale($classname, $classname, "$classname.none");
  addLocale($classname, $classname, "$classname.one");
  addLocale($classname, $classname, "$classname.all");
  addLocale($classname, $classname, "$classname-msg-create");
  addLocale($classname, $classname, "$classname-msg-modify");
  addLocale($classname, $classname, "$classname-msg-delete");
  addLocale($classname, $classname, "$classname-title-create");
  addLocale($classname, $classname, "$classname-title-modify");

  // Traductions pour la clé 
  if ($object->_spec->key) {
    $prop = $object->_spec->key;
    addLocale($classname, $prop, "$classname-$prop");
    addLocale($classname, $prop, "$classname-$prop-desc");
    addLocale($classname, $prop, "$classname-$prop-court");
  }
  
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
    
    if ($spec instanceof CRefSpec && $prop[0] != "_") {
      if ($spec->meta && $object->_specs[$spec->meta] instanceof CEnumSpec) {
        $classes = $object->_specs[$spec->meta]->_list;
        foreach ($classes as $fwdClass) {
          $fwdObject = new $fwdClass;
          
          // Find corresponding back name
          $backName = array_search("$spec->className $spec->fieldName", $fwdObject->_backProps);
          addLocale($classname, $prop, "$spec->class-back-$backName");
          addLocale($classname, $prop, "$spec->class-back-$backName.empty");
        }
      }
      else {
        $fwdClass = $spec->class;
        $fwdObject = new $fwdClass;
        
        // Find corresponding back name
        $backName = array_search("$spec->className $spec->fieldName", $fwdObject->_backProps);
        addLocale($classname, $prop, "$spec->class-back-$backName");
        addLocale($classname, $prop, "$spec->class-back-$backName.empty");
      }
    }
  }
  
  // Traductions pour les uniques
  foreach (array_keys($object->_spec->uniques) as $unique) {
    addLocale($classname, "Failures", "$classname-failed-$unique");
  }
}

/**
 * Add locale item for config category values
 *
 * @param string     $chapter  Chapter name
 * @param string     $category Category name
 * @param null|array $values   Key-value array when necessary
 * @param bool       $add_desc Tell wether shoud add a description locale item
 *
 * @return void
 */
function addConfigConfigCategory($chapter, $category, $values, $add_desc = true) {
  $prefix = $chapter ? "$chapter-$category" : $category;
  
  if (!is_array($values)) {
    addLocale("Config", "global", "config-$prefix");
    if ($add_desc) {
      addLocale("Config", "global", "config-$prefix-desc");
    }
    return;
  }
  
  foreach ($values as $key => $value) {
    addLocale("Config", $category, "config-$prefix-$key");
    if ($add_desc) {
      addLocale("Config", $category, "config-$prefix-$key-desc");
    }
  }
}

if ($module && $module != "common") {
  $model = CConfiguration::getModel();
  $features = array();
  foreach ($model as $_model) {
    foreach ($_model as $_feature => $_submodel) {
      if (strpos($_feature, $module) === 0) {
        $parts = explode(" ", $_feature);
        array_shift($parts); // Remove module name
        $item = array_pop($parts);   // Remove config name
        $prefix = implode("-", $parts);
        if (!isset($features[$prefix])) {
          $features[$prefix] = array();
        }

        $features[$prefix][$item] = $item;
      }
    }
  }

  foreach ($features as $_prefix => $values) {
    addConfigConfigCategory($module, $_prefix, null, false);
    addConfigConfigCategory($module, $_prefix, $values);
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

if ($module != "common") {
  $files = CAppUI::readFiles("modules/$module", '\.php$');
  
  addLocale("Module", "Name", "module-$module-court");
  addLocale("Module", "Name", "module-$module-long");
  
  foreach ($files as $_file) {
    $_tab = substr($_file, 0, -4);
    
    if (in_array($_tab, array("setup", "index", "config", "preferences", "configuration"))) {
      continue;
    }
    
    addLocale("Module", "Tabs", "mod-$module-tab-$_tab");
  }
}

$empty_locales = array_fill(0, 5, null);

foreach ($all_locales as &$_locale) {
  $_locale = str_replace(array('\n', '\t'), array("\n", "\t"), $_locale);
}

$completion = round(100 * $local_count / $total_count);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("total_count"  , $total_count);
$smarty->assign("local_count"  , $local_count);
$smarty->assign("completion"   , $completion);
$smarty->assign("items"        , $items);
$smarty->assign("archives"     , $archives);
$smarty->assign("completions"  , $completions);
$smarty->assign("locales"      , array_keys($localesDirs));
$smarty->assign("modules"      , $modules);
$smarty->assign("module"       , $module);
$smarty->assign("trans"        , $trans);
$smarty->assign("language"     , $language);
$smarty->assign("other_locales", $all_locales);
$smarty->assign("empty_locales", $empty_locales);

$smarty->display("mnt_traduction_classes.tpl");
