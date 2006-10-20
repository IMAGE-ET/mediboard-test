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


$aChamps = array();
$object = new $selClass;

// Extraction des champs
foreach ($object->getProps() as $k => $v) {
  $aChamps[$k]["class_field"] = $k;
  $aChamps[$k]["class_props"] = null;
  $aChamps[$k]["BDD_name"]    = null;
  $aChamps[$k]["BDD_type"]    = null;
  $aChamps[$k]["BDD_null"]    = null;
  $aChamps[$k]["BDD_default"] = null;
  $aChamps[$k]["BDD_index"]   = null;
}  

// Extraction des propriétés
foreach($object->_props as $k => $v) {
  $aChamps[$k]["class_props"] = $v;
  if(!isset($aChamps[$k]["class_field"])){
    $aChamps[$k]["class_field"] = null;
    $aChamps[$k]["BDD_name"]    = null;
    $aChamps[$k]["BDD_type"]    = null;
    $aChamps[$k]["BDD_null"]    = null;
    $aChamps[$k]["BDD_default"] = null;
    $aChamps[$k]["BDD_index"]   = null;
  }
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

  if(!isset($aChamps[$currField["Field"]]["class_field"])){
    $aChamps[$currField["Field"]]["class_field"] = null;
    $aChamps[$currField["Field"]]["class_props"] = null;
  }
}

// Extraction des Index
$sql = "SHOW INDEX FROM `".$object->_tbl."`";
$listIndex = db_loadList($sql);
foreach($listIndex as $currIndex){
  if($aChamps[$currIndex["Column_name"]]["BDD_index"]){
    $aChamps[$currIndex["Column_name"]]["BDD_index"] .= ", ";
  }
  $aChamps[$currIndex["Column_name"]]["BDD_index"] .= $currIndex["Key_name"];
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("aChamps"   , $aChamps);
$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);

$smarty->display("mnt_table_classes.tpl");
?>