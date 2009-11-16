<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

set_time_limit(30);

$ds = CSQLDataSource::get("Transit");

$tableCount = 0;
$groups = array();
foreach ($ds->loadColumn("SHOW TABLE STATUS", null) as $table) {
  if (++$tableCount > 2000) {
    break;
  }
  $columns = $ds->loadColumn("SHOW COLUMNS FROM `$table`", null);
  $tokens = split("_", $table);
  $prefix = $tokens[0];
  $groups[join($columns, ",")][$prefix][] = $table;
}

$groupCount = 0;
foreach ($groups as $subgroups) {
  foreach ($subgroups as $prefix => $group) {
    $tableCount = count($group);
    if ($tableCount > 1) {
      $tableNames = join($group, ", ");
      $groupCount++;

      // Find intersection name
      $tablesTokens = array();
      foreach ($group as $table) {
        $tablesTokens[$table] = split("_", $table);  
      }
      
      $intersection = call_user_func_array("array_intersect", $tablesTokens);
      $tableName = join("_", $intersection);
      $difference = array_keys(call_user_func_array("array_diff", $tablesTokens));

//      ROM: PROBLEME BIZARRE AVEC UN ARRAY_DIFF QUI DEVRAIT RETOURNER 2 VALEURS
//      if (count($difference) != 1) {
//        mbTrace($tablesTokens);
//        mbTrace(call_user_func_array("array_diff", $tablesTokens), "Difference");
//      }

      $dataTokenKey = $difference[0];

      CAppUI::stepAjax("Group with $tableCount tables with table name '$tableName' and data key '$dataTokenKey':\n$tableNames", UI_MSG_WARNING);

    }
  }
}

CAppUI::stepAjax("$groupCount tables groups to be marged", UI_MSG_WARNING);

?>