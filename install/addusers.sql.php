<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

?>

<?php 
$queries = array();
foreach ($dbConfigs as $dbConfigName => $dbConfig) {
  $host = $dbConfig["dbhost"];
  $name = $dbConfig["dbname"];
  $user = $dbConfig["dbuser"];
  $pass = $dbConfig["dbpass"];
  
  // Create database
  $queries[] = "CREATE DATABASE `$name` ;";

  // Standard database  
  if ($dbConfigName == "std") {
    // Create user with global permissions
    $queries[] = "GRANT USAGE, RELOAD " .
      "\nON * . * " .
      "\nTO '$user'@'$host'" .
      "\nIDENTIFIED BY '$pass';";
  }
  // Other databases
  else {
    // Create user with global permissions
    $queries[] = "GRANT USAGE" .
      "\nON * . * " .
      "\nTO '$user'@'$host'" .
      "\nIDENTIFIED BY '$pass';";
  }
      
  // Grant user with database permissions
  $queries[] = "GRANT SELECT , INSERT , UPDATE , DELETE , CREATE , DROP , INDEX , ALTER , CREATE TEMPORARY TABLES" .
    "\nON `$name` . *" .
    "\nTO '$user'@'$host';";

}

?>
