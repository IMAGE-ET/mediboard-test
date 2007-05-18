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

//Extraction des propriétés 'spec'
foreach($classSelected as $selected) {
	$object = new $selected;
    $backRefs[$selected]=$object->_backRefs;	
	foreach ($object->_specs as $objetRefSpec) {
		if (is_a($objetRefSpec, 'CRefSpec')) {
	      $spec = array();
	      $spec[] = $objetRefSpec->className;
	      $spec[] = $objetRefSpec->fieldName;
	      if ($objetRefSpec->meta) {
	      	$spec[] = $objetRefSpec->meta;
	      }
		  $backSpecs[$objetRefSpec->class][] = join($spec, " ");
   		}
	}
}

$tab = array();
$tabKey = array();

foreach($backRefs as $keyBackRef => $valueBackRef) {
	foreach ($valueBackRef as $key => $value) {
		$ok = is_numeric($key) ? "okn" : "ok";
		$tab[$keyBackRef][$value]["real"] = in_array($value,$backSpecs[$keyBackRef]) ? $ok : "r";
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
		
		if (!has_default_constructor($keyBackSpec) && count(split(" ",$value)) != 3) {
			$alert = "noMeta";
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
	foreach($valueTab as $key => $value) {
		foreach($value as $keyType => $valueType) {
			if($keyType == "real") {
				$compteurReal = count($keyType);
				/*$suggestion = $valueType == "ok" ? "Pas de suggestion." : "Penser a changer le nom des références.";
				if($valueType == "r") {
					$suggestion =  "La ou les référence(s) ";
					foreach($reference as $keyRef => $valueRef) {
						$suggestion .= $valueRef;
					}
					$suggestion .= " sont à enlever.";
				}*/
			} else { $compteurTheo = count($keyType); }
			if($compteurTheo != 0) { 
				if($compteurReal == $compteurTheo) {
					
				}
			}
			/*if($value == "ok" || $value == "okn") {
				$suggestion = "Pas de suggestion.";
			} elseif($value == "t") {
				$reference[] = 	$key;		
				$suggestion = "function getBackRefs() {\n      \$backRefs = parent::getBackRefs();\n";
				foreach($reference as $keyRef => $valueRef) {
					$suggestion .="      \$backRefs[\"$keyRef\"] = \"$valueRef\";\n";
				}
				$suggestion .="     return \$backRefs;\n}";
			} else {
				$reference[] = 	$key;
				$suggestion =  "La ou les référence(s) ";
				foreach($reference as $keyRef => $valueRef) {
					$suggestion .= $valueRef;
				}
				$suggestion .= " sont à enlever.";
			}*/
			$tabSuggestions[$keyTab] = "\n$suggestion";
		}
	}
}
mbTrace($tab);
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);
$smarty->assign("tabSuggestions"   , $tabSuggestions);
$smarty->assign("tabKey" , $tabKey);
$smarty->assign("tab"       , $tab);
$smarty->display("mnt_backref_classes.tpl");

?>