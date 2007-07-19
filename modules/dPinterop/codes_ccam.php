<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
set_time_limit(1800);


$sql = "SELECT `operation_id` , `codes_ccam`, `CCAM_code` , `CCAM_code2`" .
  "\nFROM `operations`" .
  "\nWHERE (`codes_ccam` = '' OR `codes_ccam` IS NULL)" .
  "\nAND (`CCAM_code` != '' OR `CCAM_code` IS NOT NULL)";

$res = $ds->exec( $sql );
$i = 0;

while ($obj = $ds->fetchObject($res)) {
  $i++;
  $obj->codes_ccam = $obj->CCAM_code;
  if ($obj->CCAM_code2) {
    $obj->codes_ccam .= "|$obj->CCAM_code2";
  }
    
  $sql2 = "UPDATE `operations` " .
    "\nSET `codes_ccam` = '$obj->codes_ccam' " .
    "\nWHERE `operation_id` = $obj->operation_id";
  $ds->exec($sql2); $ds->error();
};

echo "$i interventions mises à jour";

?>