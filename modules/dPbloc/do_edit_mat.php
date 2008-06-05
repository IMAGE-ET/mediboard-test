<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
$ds = CSQLDataSource::get("std");

$id = dPgetParam($_GET, 'id', 0);
$value = dPgetParam($_GET, 'value', 'n');
if($id) {
  $sql = "UPDATE operations
          SET commande_mat = '$value'
          WHERE operation_id = '$id'";
  $result = $ds->exec($sql);
}

global $AppUI;
$AppUI->redirect();
?>