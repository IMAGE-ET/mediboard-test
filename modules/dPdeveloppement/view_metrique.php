<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $can;
$can->needsRead();

$ds = CSQLDataSource::get("std");

// Nom des classes à récupérer
$listesClasses = array(
  'CUser', 'CSalle', 'CPatient', 'CChambre', 'CLit', 
  'CConsultation', 'CCompteRendu', 'CSejour', 'COperation', 
  'CFile', 'CPrescription', 'CPrescriptionLineMedicament', 
  'CNaissance', 'CRPU'
);

$result = array();
foreach ($listesClasses as $class){
	$object = new $class;
  $sql = "SHOW TABLE STATUS LIKE '{$object->_spec->table}'";
  $statusTable = $ds->loadList($sql);
  if ($statusTable) {
    $result[$class] = $statusTable[0];
    $result[$class]["Update_relative"] = CMbDate::relative($result[$class]["Update_time"]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("result" , $result);
$smarty->display("view_metrique.tpl");

?>