<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

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



foreach($classSelected as $selected){  
  $object = new $selected;  
  $nameKeyTable = $AppUI->_($selected)." - ".$selected." (".$object->_ref_module->mod_name.")";
  
  $aChamps[$nameKeyTable] = array();
  $aClass =& $aChamps[$nameKeyTable];
  
  // CLé dela table
  $aClass[$object->_tbl_key]["keytable"] = $object->_tbl_key;
  
  // Extraction des champs
  foreach ($object->getProps() as $k => $v) {
    if($k[0] != "_"){
      $aClass[$k]["class_field"] = $k;
      if(isset($object->_specs[$k])){
        $aClass[$k]["object_spec"] = $object->_specs[$k]->getDBSpec();
      }
    }
  } 
  
  // Extraction des propriétés
  foreach($object->_props as $k => $v) {
    if($k[0] != "_"){
      $aClass[$k]["class_props"] = $v;
    }
  }
  
  //Extraction des champs de la BDD
  if(db_loadTable($object->_tbl)) {
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
	  $sql = "SHOW INDEX FROM `$object->_tbl`";
    
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
    if($curr_champ["BDD_type"] && $curr_champ["class_field"]){
      if($curr_champ["class_field"] == $curr_champ["keytable"] && $curr_champ["BDD_type"] !== "int(11) unsigned"){
        $curr_champ["error_BDD_type"] = "int(11) unsigned";
      }
    }
    
    // Test sur les propriétés
    if($curr_champ["BDD_name"] && $curr_champ["class_props"]){
      $specFragments = explode("|", $curr_champ["class_props"]);
      $type_sql = $curr_champ["object_spec"];
      
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aChamps"   , $aChamps);
$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);
$smarty->display("mnt_table_classes.tpl");
?>