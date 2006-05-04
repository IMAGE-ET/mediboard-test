<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

$type         = dPgetParam($_GET, 'type'        , 0);
$operation_id = dPgetParam($_GET, 'operation_id', 0);
$anesth       = dPgetParam($_GET, 'anesth'      , null);
$del          = dPgetParam($_GET, 'del'         , 0);
$hour         = dPgetParam($_GET, 'hour'        , date("H:i:00"));
if($type) {
  if($del) {
    $sql = "UPDATE operations
            SET $type = null
            WHERE operation_id = '$operation_id'";
    $result = db_exec($sql);
  } else {
    $sql = "UPDATE operations
            SET $type = '$hour'
            WHERE operation_id = '$operation_id'";
    $result = db_exec($sql);
  }
}

if($anesth !== null) {
  $listAnesth = dPgetSysVal("AnesthType");
  $lu = null;
  foreach($listAnesth as $key => $value) {
    if(trim($value) == $anesth) {
      $lu = $key;
    }
  }
  $sql = "UPDATE operations
          SET type_anesth = '$lu'
          WHERE operations.operation_id = '$operation_id'";
  $result = db_exec($sql);
}

$AppUI->redirect();

?>