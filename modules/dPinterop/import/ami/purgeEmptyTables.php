<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

set_time_limit(30);

global $AppUI;
$ds = CSQLDataSource::get("Transit");

$tableCount = 0;
$emptyTableCount = 0;
foreach ($ds->loadColumn("SHOW TABLE STATUS", null) as $table) {
  if (!$ds->loadResult("SELECT COUNT(*) FROM `$table`")) {
    $tableCount++;
  }
}

$AppUI->stepAjax("$tableCount empty tables have to be removed", UI_MSG_WARNING);

?>