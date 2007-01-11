<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: $
* @author Thomas Despoix
*/

set_time_limit(30);

global $AppUI;

$base = "Transit";
do_connect($base);

$tableCount = 0;
$emptyTableCount = 0;
foreach (db_loadColumn("SHOW TABLE STATUS", null, $base) as $table) {
  if (!db_loadResult("SELECT COUNT(*) FROM `$table`", $base)) {
    $tableCount++;
  }
}

$AppUI->stepAjax("$tableCount empty tables have to be removed", UI_MSG_WARNING);

?>