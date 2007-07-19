<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$selClass = mbGetValueFromGetOrSession("selClass", null);

$classSelected = array();
$aChamps = array();
// Liste des Class
$listClass = getInstalledClasses();

$class_exist = array_search($selClass, $listClass);
if($class_exist=== false){
  $selClass = null;
  mbSetValueToSession("selClass", $selClass);
  $classSelected =& $listClass;
}else{
  $classSelected[] = $selClass;
}

foreach ($classSelected as $selected){  
  $object = new $selected;
  
  $aChamps[$selected] = array();
  $aClass =& $aChamps[$selected];
  
  // CLé dela table
  $aClass[$object->_tbl_key]["keytable"] = $object->_tbl_key;
  
  // Extraction des champs
  foreach ($object->getProps() as $k => $v) {
    $aClass[$k]["class_field"] = $k;
    
    if ($spec = @$object->_specs[$k]) {
      $aClass[$k]["object_spec"] = $spec->getDBSpec();
    }
  } 
  
  // Extraction des propriétés
  foreach($object->_props as $k => $v) {
    if($k[0] != "_"){
      $aClass[$k]["class_props"] = $v;
    }
  }
  
  //Extraction des champs de la BDD
  if($ds->loadTable($object->_tbl)) {
	  $sql = "SHOW FULL FIELDS FROM `".$object->_tbl."`";
	  $listFields = $ds->loadList($sql);
	  foreach($listFields as $currField){
	  	$aBdd_field =& $aClass[$currField["Field"]];
	    $aBdd_field["BDD_name"]    = $currField["Field"];
	    $aBdd_field["BDD_type"]    = $currField["Type"];
	    $aBdd_field["BDD_null"]    = $currField["Null"];
	    $aBdd_field["BDD_default"] = $currField["Default"];
	    $aBdd_field["BDD_index"]   = null;
	  }
	  
	  // Extraction des Index
	  $sql = "SHOW INDEX FROM `$object->_tbl`";
    
	  $listIndex = $ds->loadList($sql);
	  foreach($listIndex as $currIndex) {
	    if($aClass[$currIndex["Column_name"]]["BDD_index"]){
	      $aClass[$currIndex["Column_name"]]["BDD_index"] .= ", ";
	    }
	    if($currIndex["Key_name"]=="PRIMARY"){
	      $aClass[$currIndex["Column_name"]]["BDD_primary"] = true;
	    }
	    $aClass[$currIndex["Column_name"]]["BDD_index"] .= $currIndex["Key_name"];
	  }
  } 
}

$aChampsObligatoire = array(
  "keytable", 
  "class_field", 
  "class_props",
  "BDD_name",
  "BDD_type",
  "BDD_null",
  "BDD_default",
  "BDD_index",
  "BDD_primary", 
  "object_spec"
);

// Test de concordance
foreach($aChamps as $nameClass=>$currClass){
  foreach($currClass as $k=>$valueChamps){
    $curr_champ =& $aChamps[$nameClass][$k];
    
    // Ajout des champs manquants
    foreach($aChampsObligatoire as $VerifChamps){
      if(!isset($curr_champ[$VerifChamps])){
        $curr_champ[$VerifChamps] = null;
      }
    }
  
    // Ajout des champs de controle d'erreur
    $curr_champ["error_BDD_null"]    = null;
    $curr_champ["error_BDD_type"]    = null;
    $curr_champ["error_class_props"] = null;
    
    // Test clé de table
    if ($curr_champ["keytable"]) {
      if ($curr_champ["BDD_type"]) {
        if ($curr_champ["class_field"] == $curr_champ["keytable"] && $curr_champ["BDD_type"] !== "int(11) unsigned"){
          $curr_champ["error_BDD_type"] = "INT(11) UNSIGNED";
        }
      }
      else {
        $curr_champ["object_spec"] = "INT(11) UNSIGNED";
      }
    }
    
    // Test sur les propriétés
    if($curr_champ["BDD_name"] && $curr_champ["class_props"]){
      $type_sql = $curr_champ["object_spec"];
      
      if (strtoupper($curr_champ["BDD_type"]) != strtoupper($type_sql)) {
        $curr_champ["error_class_props"] = true;
        $curr_champ["error_BDD_type"]    = $type_sql;
      }
      
      //Test notNull et YES dans BDD
      $specFragments = explode(" ", $curr_champ["class_props"]);
      $notNull = array_search("notNull", $specFragments);
      if($notNull && $curr_champ["BDD_null"]=="YES"){
        $curr_champ["error_BDD_null"] = true;
        $curr_champ["error_class_props"] = true;
      }
    }
    
    
    // Supressions des lignes correctes dans le mode d'affichage "liste des erreurs"
    if($selClass===null){ 
    	// Aucun champs d'erreur
      $test_champs_valide = !$curr_champ["error_BDD_null"] && !$curr_champ["error_BDD_type"] && !$curr_champ["error_class_props"];
      // ET -- Correspondance  Field et BDD
      $test_champs_valide = $test_champs_valide && $curr_champ["BDD_name"] && $curr_champ["class_field"];
      // ET -- clé primaire sans spec OU spec sans clé primaire
      $test_champs_valide = $test_champs_valide && ($curr_champ["class_props"] XOR $curr_champ["BDD_primary"]);
      $test_champs_valide = $test_champs_valide && !($curr_champ["keytable"] && $curr_champ["keytable"]==$curr_champ["class_field"] && $curr_champ["class_props"]);
      
      if($test_champs_valide){
        unset($aChamps[$nameClass][$k]);
      }
    }
  }
}

// Construction des suggestions
$aSuggestions = array();
foreach ($aChamps as $class => $aFields) {
  $object = new $class;
  $newTable = !$ds->loadTable($object->_tbl);

  // Production de chaque item de suggestion
  foreach ($aFields as $fieldName => $fieldInfo) {
    $BDD_name = $fieldInfo["BDD_name"];
    $error_BDD_type = $fieldInfo["error_BDD_type"];
    $error_BDD_null = $fieldInfo["error_BDD_null"];
    $class_props = $fieldInfo["class_props"];
    $class_field = $fieldInfo["class_field"];
    $BDD_sugg = $fieldInfo["object_spec"];
    
    $suggestion = null;
    
    if ($error_BDD_type || $error_BDD_null || !$BDD_name) {
      $fieldCreateKeyword = $newTable ? "" : "ADD";
      
      $suggestion  = $BDD_name ?
        "CHANGE `$fieldName` `$fieldName` $error_BDD_type" : 
        "$fieldCreateKeyword `$fieldName` $BDD_sugg";
      
      if ($fieldInfo["keytable"]) {
        $suggestion .= " NOT NULL AUTO_INCREMENT"; 
      } 

      if (false !== array_search("notNull", explode(" ", $class_props))) {
        $suggestion .= " NOT NULL";
      }
    }
    
    if ($BDD_name && !$class_field) {
      $suggestion = "DROP `$BDD_name`";
    }
    
    if ($suggestion) {
      $aSuggestions[$class][] = "\n$suggestion";
    }
  
  }
  
  // Production de la suggestion pour la table complete
  if (array_key_exists($class, $aSuggestions)) {
    if ($newTable) {
      $aSuggestions[$class] = "CREATE TABLE `$object->_tbl` (" . 
        join($aSuggestions[$class], ", ") . 
        ", \nPRIMARY KEY (`$object->_tbl_key`)) TYPE=MYISAM;";
    }
    else {
      $aSuggestions[$class] = "ALTER TABLE `$object->_tbl`" . 
        join($aSuggestions[$class], ", ") . 
        ";";
    }
  }
  else {
    $aSuggestions[$class] = null;
  }

}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aChamps"   , $aChamps);
$smarty->assign("aSuggestions"   , $aSuggestions);
$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);
$smarty->display("mnt_table_classes.tpl");
?>