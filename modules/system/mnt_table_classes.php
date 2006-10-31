<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $m;

if (!$canRead) {
    $AppUI->redirect( "m=system&a=access_denied" );
}

$selClass = mbGetValueFromGetOrSession("selClass", null);


function getDBSpec($spec){
  $type_sql = null;

  $specFragments = explode("|", $spec);
  
  // Suppression "confidential"
  $confidential = array_search("confidential", $specFragments);
  if ($confidential !== false) {
    array_splice($specFragments, $confidential, 1);
  }
  // Suppresion "notNull"
  $notNull = array_search("notNull", $specFragments);
  if ($notNull !== false) {
    array_splice($specFragments, $notNull, 1);
  }
  
  switch ($specFragments[0]) {
    case "ref":
      $type_sql = "int(11) unsigned";
      break;
    
    case "bool":
      $type_sql = "enum('0','1')";
      break;
      
    case "numchar":
      $type_sql = "bigint zerofill";
      if(isset($specFragments[1])){
      	switch ($specFragments[1]) {
      	  case "maxLength":
          case "length":
            $length = $specFragments[2];
            $valeur_max = pow(10,$length);
            $type_sql = "tinyint";
            if ($valeur_max > pow(2,8)) {
              $type_sql = "mediumint";
            }
            if ($valeur_max > pow(2,16)) {
              $type_sql = "int";
            }
            if ($valeur_max > pow(2,32)) {
              $type_sql = "bigint";
            }
            $type_sql .= "($length) unsigned zerofill";
        }
      }
      break;
    
    case "str":
      $type_sql = "varchar(255)";
      if(isset($specFragments[1])){
        switch ($specFragments[1]) {
          case "maxLength":
          case "length":
            $type_sql = "varchar(".$specFragments[2].")";
            break;
          case "max":
            $type_sql = "varchar(".strlen($specFragments[2]).")";
            break;
        }
      }
      break;
      
    case "num":
      $type_sql = "int(11)";
      if(isset($specFragments[1])){
        $valeur_max = null;
        switch ($specFragments[1]) {
          case "minMax":
            $valeur_max = $specFragments[3];
          case "max":
            if(!$valeur_max){
              $valeur_max = $specFragments[2];
            }
            $type_sql = "tinyint(4)";
            if ($valeur_max > pow(2,8)) {
              $type_sql = "mediumint(9)";
            }
            if ($valeur_max > pow(2,16)) {
              $type_sql = "int";
            }
            if ($valeur_max > pow(2,32)) {
              $type_sql = "bigint";
            }
            break;
          case "pos":
            $type_sql = "int(11) unsigned";
            break;          
        }
      }
      break;
    
    case "pct":
    case "float":
    case "currency":
      $type_sql = "float";
      if(isset($specFragments[1])){
        switch ($specFragments[1]) {
          case "pos":
            $type_sql = "float unsigned";
            break;          
        }
      }
      break;

    case "enum":
      array_shift($specFragments);
      $type_sql = "enum('".implode("','", $specFragments)."')";
      break;
      
    case "dateTime":
      $type_sql = "datetime";
      break;

    case "date":
      $type_sql = "date";
      break;

    case "time":
      $type_sql = "time";
      break;
    
    case "email":
      $type_sql = "varchar(50)";
      break;
      
    case "text":
      $type_sql = "text";
      break;
      
    case "html":
      $type_sql = "mediumtext";
      break;

    case "code":
      switch (@$specFragments[1]) {
        case "ccam":
          $type_sql = "varchar(7)";
          break;
        case "cim10":
          $type_sql = "varchar(5)";
          break;
        case "adeli":
          $type_sql = "varchar(9)";
          break;
        case "insee":
          $type_sql = "varchar(15)";
          break;
      }
      break;
  }
  return $type_sql;
}


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



foreach($classSelected as $selected){  
  $object = new $selected;  
  $nameKeyTable = $AppUI->_($selected)." - ".$selected." (".$object->_ref_module->mod_name.")";
  
  $aChamps[$nameKeyTable] = array();
  $aClass =& $aChamps[$nameKeyTable];
  
  // CLé dela table
  $aClass[$object->_tbl_key]["keytable"] = $object->_tbl_key;
  
  // Extraction des champs
  foreach ($object->getProps() as $k => $v) {
    $aClass[$k]["class_field"] = $k;
  } 
  
  // Extraction des propriétés
  foreach($object->_props as $k => $v) {
    $aClass[$k]["class_props"] = $v;    
  }
  
  //Extraction des champs de la BDD
  $sql = "SHOW FULL FIELDS FROM `".$object->_tbl."`";
  $listFields = db_loadList($sql);
  foreach($listFields as $currField){
  	$aBdd_field =& $aClass[$currField["Field"]];
    $aBdd_field["BDD_name"]    = $currField["Field"];
    $aBdd_field["BDD_type"]    = $currField["Type"];
    $aBdd_field["BDD_null"]    = $currField["Null"];
    $aBdd_field["BDD_default"] = $currField["Default"];
    $aBdd_field["BDD_index"]   = null;
  }
  
  // Extraction des Index
  $sql = "SHOW INDEX FROM `".$object->_tbl."`";
  $listIndex = db_loadList($sql);
  foreach($listIndex as $currIndex){
    if($aClass[$currIndex["Column_name"]]["BDD_index"]){
      $aClass[$currIndex["Column_name"]]["BDD_index"] .= ", ";
    }
    if($currIndex["Key_name"]=="PRIMARY"){
      $aClass[$currIndex["Column_name"]]["BDD_primary"] = true;
    }
    $aClass[$currIndex["Column_name"]]["BDD_index"] .= $currIndex["Key_name"];
  }
  
}

$aChampsObligatoire = array("keytable", "class_field","class_props","BDD_name","BDD_type",
                             "BDD_null","BDD_default","BDD_index","BDD_primary");


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
    if($curr_champ["BDD_type"] && $curr_champ["class_field"]){
    	$type_sql = getDBSpec("ref");
      if($curr_champ["class_field"]==$curr_champ["keytable"]
        && $curr_champ["BDD_type"]!=$type_sql){
        $curr_champ["error_BDD_type"] = $type_sql;
      }
    }
    
    // Test sur les propriétés
    if($curr_champ["BDD_name"] && $curr_champ["class_props"]){
      $specFragments = explode("|", $curr_champ["class_props"]);
      // Champs BDD et props
      $type_sql = getDBSpec($curr_champ["class_props"]);
      if($curr_champ["BDD_type"] != $type_sql){
        $curr_champ["error_class_props"] = true;
        $curr_champ["error_BDD_type"]    = $type_sql;
      }
      //Test notNull et YES dans BDD
      $notNull = array_search("notNull", $specFragments);
      if($notNull && $curr_champ["BDD_null"]=="YES"){
        $curr_champ["error_BDD_null"] = true;
        $curr_champ["error_class_props"] = true;
      }

    }
    
    // Si tout a afficher : on supprime les lignes valides
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


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("aChamps"   , $aChamps);
$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);
$smarty->display("mnt_table_classes.tpl");
?>