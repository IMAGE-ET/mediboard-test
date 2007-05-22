<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Yohann / Alexis
*/

global $AppUI, $can, $m;

$can->needsRead();

$selClass = mbGetValueFromGetOrSession("selClass", null);

$classSelected = array();

// Liste des Class
$listClass = getInstalledClasses();

$class_exist = array_search($selClass, $listClass);
if($class_exist === false){
  $selClass = null;
  mbSetValueToSession("selClass", $selClass);
  $classSelected =& $listClass;
}else{
  $classSelected[] = $selClass;
}

$backSpecs = array();
$backRefs = array();

// Extraction des propriétés 'spec' (théorique)
foreach($classSelected as $selected) {
  $object = new $selected;
    $backRefs[$selected]=$object->_backRefs;  
  foreach ($object->_specs as $objetRefSpec) {
    if (is_a($objetRefSpec, 'CRefSpec')) {
    
        $spec = array();
        $spec[] = $objetRefSpec->className;
        $spec[] = $objetRefSpec->fieldName;
        
      $backSpecs[$objetRefSpec->class][] = join($spec, " ");
       }
  }
}

$tab = array();
$tabKey = array();

// Analyse des réels
foreach ($backRefs as $keyBackRef => $valueBackRefs) {
  foreach ($valueBackRefs as $key => $backRef) {
    $ok = is_numeric($key) ? "warningNum" : "ok";
    if (@$backSpecs[$keyBackRef]) {
      $realRef =& $tab[$keyBackRef][$backRef]["real"];
      $realRef["condition"] = $ok; 
      $realRef["attribut"] = $key;
      // Vérification que la fwd ref cible bien la class et non un ancêtre
      if (!in_array($backRef,$backSpecs[$keyBackRef])) {
        $backRefParts = split(" ", $backRef);
        $fwdClass = $backRefParts[0];
        $fwdField = $backRefParts[1];
	    if (!class_inherits_from($fwdClass, "CMbObject") || !has_default_constructor($fwdClass)) {
          $realRef["condition"] = "noCMbObject";
	      continue;
	    }
        $fwdObject = new $fwdClass;
        if (null == $fwdSpec = @$fwdObject->_specs[$fwdField]) {
          $realRef["condition"] = "default";
          continue; 
        } 
        if ($fwdSpec->class != $keyBackRef) {
          unset($tab[$keyBackRef][$backRef]);
          continue;
        }  
        $realRef["condition"] = "default";
      }
    } 
  }
}

foreach($backSpecs as $keyBackSpec => $valueBackSpec) {
  foreach ($valueBackSpec as $key => $value) {
    $alert =& $tab[$keyBackSpec][$value]["theo"];
    if (!class_exists($keyBackSpec)) {
      $alert = "noClass";
      continue;
    }
    if (!class_inherits_from($keyBackSpec, "CMbObject")) {
      $alert = "noCMbObject";
      continue;
    }
    $alert = array_key_exists($keyBackSpec,$backRefs) && in_array($value,$backRefs[$keyBackSpec]) ? "ok" : $alert;
  }
}
$aSuggestions = array();

// Construction des suggestions
foreach($tab as $keyTab => $valueTab) {
  $suggestion = null;
  $reference = array();
  $compteurReal = $compteurTheo = $compteurOkn = 0;
  foreach($valueTab as $key => $value) {
    if(@$tab[$keyTab][$key]["real"]["condition"]) {
      if($tab[$keyTab][$key]["real"]["condition"] == "warningNum") {
        $compteurOkn++;
      }
        $compteurReal++;
    }
    $compteurTheo++;
    
    $reference[] =  $key;  
  }
  if($compteurReal == $compteurTheo) {
    $suggestion = $compteurOkn > 0 ? "Penser a changer le nom des références." : "Pas de suggestion.";
  } elseif($compteurReal > $compteurTheo) {
    $suggestion = "Attention. Une ou des référence(s) sont à enlever.";
  } elseif($compteurReal < $compteurTheo) {
  	  	$suggestion = "La classe n'est pas instanciable'.";
  	  	$suggestion = "function getBackRefs() {\n      \$backRefs = parent::getBackRefs();\n";
        foreach($reference as $keyRef => $valueRef) {
          $suggestion .="      \$backRefs[\"$keyRef\"] = \"$valueRef\";\n";
        }
        $suggestion .="     return \$backRefs;\n}";
  }
    $tabSuggestions[$keyTab] = "\n$suggestion";
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);
$smarty->assign("tabSuggestions"   , $tabSuggestions);
$smarty->assign("tabKey" , $tabKey);
$smarty->assign("tab"       , $tab);
$smarty->display("mnt_backref_classes.tpl");

?>