<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

set_time_limit(300);

global $AppUI;
$ds = CSQLDataSource::get("Transit");
$dsn = "AMI";

// DSN Connection
if (null == $link = odbc_connect($dsn, "", "")) {
  $AppUI->stepAjax("Couldn't connect to '$dsn' ODBC DSN'", UI_MSG_ERROR);
}

// DSN Table analysis
$res = odbc_columns($link);
$tables = array();
while ($column = odbc_fetch_object($res)) {
  // System table exclusion
  if (strpos($column->TABLE_NAME, "MSys") === 0) {
    continue;
  }
  $tables[$column->TABLE_NAME][$column->ORDINAL] = $column->COLUMN_NAME;
}

$AppUI->stepAjax("Tables count: " . count($tables));


// Check table conrrespondance 
$rowCountError = 0;
$columnsError = 0;
$tableMissing = 0;

foreach ($tables as $table => $columns) {
  if ($ds->loadTable($table)) {
    // Rows count
    $query = "SELECT COUNT(*) AS  total FROM `$table`";
    $res = odbc_exec($link, $query);
    $rowCount = odbc_result($res, "total");
  
    $rowCountCopy = $ds->loadResult("SELECT COUNT(*) FROM `$table`");
    if ($rowCount != $rowCountCopy) {
      $AppUI->stepAjax("Rows count for table '$table' differ, $rowCountCopy instead of $rowCount", UI_MSG_WARNING);
      $tableMissing++;
    }

    // Column count
    $columnsCopy = $ds->loadColumn("SHOW COLUMNS FROM `$table`", null);
    if (array_values($columns) != array_values($columnsCopy)) {
      $AppUI->stepAjax("Columns names for table '$table' differ", UI_MSG_WARNING);
      $columnsError++;
    }
  } else {
      $AppUI->stepAjax("table '$table' does not exist", UI_MSG_WARNING);
      $rowCountError++;
  }
}

if ($tableMissing) {
  $AppUI->stepAjax("$tableMissing tables are missing", UI_MSG_WARNING);
} else {
  $AppUI->stepAjax("No tables are missing out of $i tables");
}

if ($rowCountError) {
  $AppUI->stepAjax("Rows count errors on $rowCountError tables", UI_MSG_WARNING);
} else {
  $AppUI->stepAjax("Rows count checked with no errors");
}

if ($columnsError) {
  $AppUI->stepAjax("Column name errors on  count errors on $columnsError tables", UI_MSG_WARNING);
} else {
  $AppUI->stepAjax("Column names checked with no errors");
}

?>