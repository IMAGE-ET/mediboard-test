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
$aChamps   = array();
// Liste des Class
$listClass = getInstalledClasses();


foreach($listClass as $selected){
  $object = new $selected;
  $nameKeyTable = CAppUI::tr($selected)." - ".$selected." (".$object->_ref_module->mod_name.")";
  
  $aChamps[$nameKeyTable] = array();
  $aClass =& $aChamps[$nameKeyTable];
  
  // Extraction des champs de prop "refMnadatory" ou "ref"
  foreach ($object->_specs as $k => $v) {
    $typeProp = $v->getSpecType();
    if($typeProp == "refMandatory" || $typeProp == "ref"){
      //Comptage du nombre d'entrées à 0
      $sql = "SELECT count(`".$object->_tbl_key."`) FROM `".$object->_tbl."` WHERE `$k` = '0';";
      $nb_result = $ds->loadResult($sql);
      
      if($nb_result){
        $aClass[$k]["class_field"] = $k;
        $aClass[$k]["typeProp"]    = $typeProp;
        $aClass[$k]["count_0_bdd"] = $nb_result;
      
        //Comptage du nombre d'entrées totale
        $sql = "SELECT count(`".$object->_tbl_key."`) FROM `".$object->_tbl."`";
        $aClass[$k]["count_bdd"] = $ds->loadResult($sql);
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aChamps"   , $aChamps);
$smarty->display("vw_refMandatory.tpl");
?>