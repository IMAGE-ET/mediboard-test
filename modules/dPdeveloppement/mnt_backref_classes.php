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
	      
		  $backSpecs[$objetRefSpec->class][] = join($spec, " ");
   		}
	}
}

$tab = array();
$tabKey = array();

foreach($backRefs as $keyBackRef => $valueBackRef) {
	foreach ($valueBackRef as $key => $value) {
		$ok = is_numeric($key) ? "okn" : "ok";
		$tab[$keyBackRef][$value]["real"]["condition"] = in_array($value,$backSpecs[$keyBackRef]) ? $ok : "r";
		$tab[$keyBackRef][$value]["real"]["attribut"] = $key;
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
		if(@$tab[$keyTab][$key]["real"]) {
			if($tab[$keyTab][$key]["real"] == "okn") {
				$compteurOkn++;
			}
		    $compteurReal++;
		}
		if(@$tab[$keyTab][$key]["theo"]) {
  			$compteurTheo++;
		}
		$reference[] =  $key;  
	}
	if($compteurReal == $compteurTheo) {
		$suggestion = $compteurOkn > 0 ? "Penser a changer le nom des références." : "Pas de suggestion.";
	} elseif($compteurReal > $compteurTheo) {
		$suggestion = "Attention. Une ou des référence(s) sont à enlever.";
	} elseif($compteurReal < $compteurTheo) {
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