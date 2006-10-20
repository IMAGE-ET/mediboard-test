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

// Liste des Class
$listClass = getInstalledClasses();
if(!$selClass){
  $selClass = current($listClass);
}


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
      $type_sql = "int(10) unsigned";
      break;

    case "str":
      $type_sql = "varchar(255)";
      if(isset($specFragments[1])){
        switch ($specFragments[1]) {
        	case "maxLength":
          case "length":
            $type_sql = "varchar(".$specFragments[2].")";
            break;
        }
      }
      break;
      
    case "num":
      $type_sql = "int(11)";
      if(isset($specFragments[1])){
        switch ($specFragments[1]) {
          case "maxLength":
          case "length":
            $type_sql = "int(".$specFragments[2].")";
            break;
          case "max":
            $type_sql = "int(".strlen($specFragments[2]).")";
            break;
          case "pos":
            $type_sql = "int(10) unsigned";
            break;          
        }
      }
      break;
    
    case "pct":
    case "currency":
      $type_sql = "float";
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



$aChamps = array();
$object = new $selClass;
$keytable = $object->_tbl_key;

// Extraction des champs
foreach ($object->getProps() as $k => $v) {
  $aChamps[$k]["class_field"] = $k;
}  

// Extraction des propriétés
foreach($object->_props as $k => $v) {
  $aChamps[$k]["class_props"] = $v;
  getDBSpec($v);
}

// Extraction des champs de la BDD
$sql = "SHOW FULL FIELDS FROM `".$object->_tbl."`";
$listFields = db_loadList($sql);
foreach($listFields as $currField){
  $aChamps[$currField["Field"]]["BDD_name"]    = $currField["Field"];
  $aChamps[$currField["Field"]]["BDD_type"]    = $currField["Type"];
  $aChamps[$currField["Field"]]["BDD_null"]    = $currField["Null"];
  $aChamps[$currField["Field"]]["BDD_default"] = $currField["Default"];
  $aChamps[$currField["Field"]]["BDD_index"]   = null;
}

// Extraction des Index
$sql = "SHOW INDEX FROM `".$object->_tbl."`";
$listIndex = db_loadList($sql);
foreach($listIndex as $currIndex){
  if($aChamps[$currIndex["Column_name"]]["BDD_index"]){
    $aChamps[$currIndex["Column_name"]]["BDD_index"] .= ", ";
  }
  if($currIndex["Key_name"]=="PRIMARY"){
    $aChamps[$currIndex["Column_name"]]["BDD_primary"] = true;
  }
  $aChamps[$currIndex["Column_name"]]["BDD_index"] .= $currIndex["Key_name"];
}

$aChampsObligatoire = array("class_field","class_props","BDD_name","BDD_type","BDD_null","BDD_default","BDD_index","BDD_primary");
// Test de concordance
foreach($aChamps as $k=>$valueChamps){
  $curr_champ =& $aChamps[$k];
	foreach($aChampsObligatoire as $VerifChamps){
    if(!isset($curr_champ[$VerifChamps])){
      $curr_champ[$VerifChamps] = null;
    }
  }
  $curr_champ["error_BDD_null"]    = null;
  $curr_champ["error_BDD_type"]    = null;
  $curr_champ["error_class_props"] = null;
  
  if($curr_champ["BDD_name"] && $curr_champ["class_props"]){
  	// Champs BDD et props
    $type_sql = getDBSpec($curr_champ["class_props"]);
    if($curr_champ["BDD_type"] != $type_sql){
      $curr_champ["error_class_props"] = true;
      $curr_champ["error_BDD_type"]    = $type_sql;
    }
  }
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("aChamps"   , $aChamps);
$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);
$smarty->assign("keytable"  , $keytable);
$smarty->display("mnt_table_classes.tpl");
?>