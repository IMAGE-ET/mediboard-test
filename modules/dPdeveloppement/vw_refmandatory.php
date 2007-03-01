<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $m;

if (!$canRead) {
    $AppUI->redirect( "m=system&a=access_denied" );
}

$aChamps   = array();
// Liste des Class
$listClass = getInstalledClasses();


foreach($listClass as $selected){
  $object = new $selected;
  $nameKeyTable = $AppUI->_($selected)." - ".$selected." (".$object->_ref_module->mod_name.")";
  
  $aChamps[$nameKeyTable] = array();
  $aClass =& $aChamps[$nameKeyTable];
  
  // Extraction des champs de prop "refMandatory"
  foreach ($object->_specs as $k => $v) {
    if($v->getSpecType() == "refMandatory"){
      //Comptage du nombre d'entrées à 0
      $sql = "SELECT count(`".$object->_tbl_key."`) FROM `".$object->_tbl."` WHERE `$k` = '0';";
      $nb_result = db_loadResult($sql);
      
      if($nb_result){
        $aClass[$k]["class_field"] = $k;
        $aClass[$k]["count_0_bdd"] = $nb_result;
      
        //Comptage du nombre d'entrées totale
        $sql = "SELECT count(`".$object->_tbl_key."`) FROM `".$object->_tbl."`";
        $aClass[$k]["count_bdd"] = db_loadResult($sql);
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aChamps"   , $aChamps);
$smarty->display("vw_refMandatory.tpl");
?>