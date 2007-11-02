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

// liste des dossiers modules + common et styles
$modules = array_merge( array("common"=>"common", "styles"=>"styles") ,$AppUI->readDirs("modules"));
CMbArray::removeValue(".svn", $modules);
ksort($modules);

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
CMbArray::removeValue(".svn",$localesDirs);
CMbArray::removeValue("en",$localesDirs);
	
$backSpecs = array();
$backRefs = array();
$trans = array();

// Extraction des propriétés 'spec' (théorique)
foreach($classSelected as $selected) {
	if ($selected == 'CModule') {
		continue;
	}
    $object = new $selected;
    // Récupération du fichier demandé pour toutes les langues
	$translateModule = new CMbConfig;
	$translateModule->sourcePath = null;
	$contenu_file = array();
	$module = $object->_ref_module->mod_name;
	foreach($localesDirs as $locale){
	  $translateModule->options = array("name" => "locales");
	  $translateModule->targetPath = "locales/fr/$modules[$module].php";
	  $translateModule->load();
	  $contenu_file[$locale] = $translateModule->values;
	}
	
	// Réattribution des clés et organisation
	
	foreach($localesDirs as $locale){
		foreach($contenu_file[$locale] as $k=>$v){
			$trans[ (is_int($k) ? $v : $k) ][$locale] = $v;
		}
	}
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

$tabInfo = array();
$tabKey = array();

// Analyse des réels
foreach ($backRefs as $keyBackRef => $valueBackRefs) {
  foreach ($valueBackRefs as $key => $backRef) {
    $ok = is_numeric($key) ? "warningNum" : "ok";
    if (@$backSpecs[$keyBackRef]) {
      $realRef =& $tabInfo[$keyBackRef][$backRef]["real"];
      $realRef["condition"] = $ok; 
      $realRef["attribut"] = $key;
      //mbTrace($keyBackRef.'-back-'.$key);
      $realRef["traduction"] = !array_key_exists($keyBackRef.'-back-'.$key,$trans) ? '' : $trans[$keyBackRef."-back-".$key]["fr"];
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
          unset($tabInfo[$keyBackRef][$backRef]);
          continue;
        }  
        $realRef["condition"] = "default";
      }
    } 
  }
}

foreach($backSpecs as $keyBackSpec => $valueBackSpec) {
  foreach ($valueBackSpec as $key => $value) {
    $alert =& $tabInfo[$keyBackSpec][$value]["theo"];
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
$tabSuggestions = array();

// Construction des suggestions
foreach($tabInfo as $keyTab => $valueTab) {
  $suggestion = null;
  $reference = array();
  $compteurReal = $compteurTheo = $compteurOkn = 0;
  foreach($valueTab as $key => $value) {
    if(@$tabInfo[$keyTab][$key]["real"]["condition"]) {
      if($tabInfo[$keyTab][$key]["real"]["condition"] == "warningNum") {
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

$smarty->assign("selClass"  		, $selClass);
$smarty->assign("listClass" 		, $listClass);
$smarty->assign("tabSuggestions"   	, $tabSuggestions);
$smarty->assign("tabKey" 			, $tabKey);
$smarty->assign("tabInfo"       	, $tabInfo);

$smarty->display("mnt_backref_classes.tpl");

?>