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

$listeClasses = getInstalledClasses();

$result = array();
foreach ($listeClasses as $class){
  $object = new $class;
  if ($object->_spec->measureable) {
	  $sql = "SHOW TABLE STATUS LIKE '{$object->_spec->table}'";
	  $statusTable = $ds->loadList($sql);
	  if ($statusTable) {
	    $result[$class] = $statusTable[0];
	    $result[$class]["Update_relative"] = CMbDate::relative($result[$class]["Update_time"]);
	  }
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("result" , $result);
$smarty->display("view_metrique.tpl");

?>