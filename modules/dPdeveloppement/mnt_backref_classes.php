<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision: $
 * @author Yohann / Alexis / Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsRead();

// Nom de la classe à prendre en compte
$class_name = mbGetValueFromGetOrSession('class_name', null);

// Liste des noms des classes installées
$list_class_names = getMbClasses();

$list_selected_classes = array(); // Liste des noms de classes selectionnées
$list_classes = array(); // Liste des classes

// On regarde si la classe existe vraiment
if(array_search($class_name, $list_class_names) == false) {
  $class_name = null;
  $list_selected_classes =& $list_class_names;
}
else {
  $list_selected_classes[] = $class_name;
}
mbSetValueToSession('class_name', $class_name);

// Renvoie une instance de la classe dont le nom est passé en parametre si elle herite de CMbObject, 
// ou bien l'instance si elle est passée, ou rien du tout
function getClass($c) {
  if (is_string($c) && class_exists($c)) return new $c;
  else if (is_subclass_of($c, 'CMbObject')) return $c;
  return;
}

// Construit les backSpecs presentes dans la classe et les renvoie
function getBackSpecs($c) {
  $o = getClass($c);
  $c = $o->_class_name;

  $backSpecs = array();
  $backRefs = $o->getBackRefs();

  foreach ($backRefs as $key => $bref) {
  	if (!isset($o->_backSpecs[$key])) continue;
    $o->makeBackSpec($key);
    $backSpec = $o->_backSpecs[$key];
    if ($backSpec->_initiator == $c) {
      $backSpecs[$key] = $backSpec;
    }
  }
  return $backSpecs;
}

global $refSpecsByTarget;
$refSpecsByTarget = array();
foreach ($list_class_names as $class) {
  $object = new $class;
  
  // Classe abstraite (non pesistente)
  if (!$object->_spec->table) {
    continue;    
  }
  
  foreach ($object->_specs as $spec) {
    // Pas une ref
    if (!$spec instanceof CRefSpec) {
      continue;
    }
    
    // Pas un DB Field
    if (!$spec->fieldName || $spec->fieldName[0] == '_') {
    	continue;
    }
    
    // Pas la clé primaire
    if ($object->_class_name == $spec->class && $object->_spec->key == $spec->fieldName) {
      continue;
    }
    
    $refSpecsByTarget[$spec->class][] = $spec;
  }
}

// Consrtuit la liste des backSpecs qui devraient etre presentes 
// dans la classe en fonction des specs de chaque classe
function getFwdSpecsTo($c, $list_classes) {  
  $o = getClass($c);
  $c = $o->_class_name;

  global $refSpecsByTarget;
  return array_key_exists($c, $refSpecsByTarget) ? $refSpecsByTarget[$c] : array();
}

// Verifie les backSpecs, en renvoyant, un tableau des specs superflues et les specs manquantes
function checkBackRefs($c, $list_classes) {
  $o = getClass($c);
  $c = $o->_class_name;
  
  $fwdSpecsTo = getFwdSpecsTo($o, $list_classes);
  $backSpecs = getBackSpecs($o);

  $missingBackRefs = array();
  $excessBackRefs = array();
  
  foreach ($fwdSpecsTo as $keyFwd => $fwdSpec) {
    $missing = true;
    foreach ($backSpecs as $keyBack => $backSpec) {
      if ($fwdSpec->className == $backSpec->class && 
          $fwdSpec->fieldName == $backSpec->field) {
        $missing = false;
        break;
      }
    }
    if ($missing) $missingBackRefs[$keyFwd] = $fwdSpec;
  }

  foreach ($backSpecs as $keyBack => $backSpec) {
    $excess = true;
    foreach ($fwdSpecsTo as $keyFwd => $fwdSpec) {
      if ($fwdSpec->className == $backSpec->class && 
          $fwdSpec->fieldName == $backSpec->field ||
          $backSpec->class == $o->_class_name) {
        $excess = false;
        break;
      }
    }
    if ($excess) $excessBackRefs[$keyBack] = $backSpec;
  }
  return array('missing' => $missingBackRefs, 'excess' => $excessBackRefs);
}

// Cherche une backref correspondante dans la liste des backref passée (fonction utilitaire)
function findBackRef($backRefs, $class, $field) {
  foreach($backRefs as $br) {
    if ($br->class == $class && $br->field == $field) return $br;
  }
  return false;
}

// Construit la suggestion de methode 'getBackrefs' pour la classe passée en paramètre, 
// avec les backRefs deja presentes + celles qui devraient y etre
function getBackRefsSuggestion($c, $list_classes) {
  $backSpecs = getFwdSpecsTo($c, $list_classes);
  $oldBackSpecs = getBackSpecs($c);
  
  $maxlength = 0;
  $sugg = array();
  $i = 0;
  foreach ($backSpecs as $backSpec) {
    $o = new $backSpec->className;
    $key = null;
    if ($br = findBackRef($oldBackSpecs, $backSpec->className, $backSpec->fieldName)) {
      $key = $br->name;
    } else if ($o->_spec->table)  {
      $key = $o->_spec->table.'s';
    }
    if ($key) {
      $key = array_key_exists($key, $sugg) ? $key.'_'.$i++ : $key;
      $maxlength = max($maxlength, strlen($key));
      $sugg[$key] = $backSpec->className.' '.$backSpec->fieldName;
    }
  }

  $ret = null;
  if (count($sugg)) {
    $ret = "function getBackRefs() {\n  \$backRefs = parent::getBackRefs();\n";
    foreach ($sugg as $key => $bs) {
      $ret .= "  \$backRefs[\"$key\"] ".str_pad('', $maxlength-strlen($key), ' ')."= \"$bs\";\n";
    }
    $ret .= "  return \$backRefs;\n}";
  }
  return $ret;
}

// Recupere la liste des traduction d'une classe (pour ses backrefs)
function getLocales($c, $locale) {
  $o = getClass($c);
  $c = $o->_class_name;
  
  $backSpecs = getBackSpecs($c);
  $locales = array();
  foreach ($backSpecs as $key => $spec) {
    $locale_key = $c.'-back-'.$key;
    $locales[$key] = isset($locale[$locale_key]) ? $locale[$locale_key] : null;
  }
  return $locales;
}

global $locales;
$list_suggestions = array();
$list_check_results = array();
$list_locales = array();
$list_backspecs = array();
foreach ($list_selected_classes as $class) {
  $list_suggestions[$class] = getBackRefsSuggestion($class, $list_class_names); 
  $list_check_results[$class] = checkBackRefs($class, $list_class_names); 
  $list_locales[$class] = getLocales($class, $locales); 
  $list_backspecs[$class] = getBackSpecs($class); 
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('class_name',            $class_name);
$smarty->assign('list_class_names',      $list_class_names);
$smarty->assign('list_selected_classes', $list_selected_classes);
$smarty->assign('list_suggestions',      $list_suggestions);
$smarty->assign('list_backspecs',        $list_backspecs);
$smarty->assign('list_locales',          $list_locales);
$smarty->assign('list_check_results',    $list_check_results);

$smarty->display('mnt_backref_classes.tpl');

?>