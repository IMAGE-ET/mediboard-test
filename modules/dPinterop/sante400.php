<?php 

require_once "DB.php";

$dsn = array (
    "phptype"  => "odbc",
    "username" => "GMB",
    "password" => "GMB  ",
    "hostspec" => "sante400",
//    "database" => "sante400",
);

//$dsn = "sante400";

$chrono = new Chronometer();

$db_user = "GMB";
$db_pass = "GMB";

$sql = "SELECT * FROM API";
  for ($index = 0; $index < 1; $index++) {

  // Create a new DB connection object and connect to the ODBC database.
  $db =& DB::connect($dsn);
  if (DB::isError($db)) {
    die("Unable to connect to database: " 
      . $db->getMessage() . "\n"
      . $db->getDebugInfo() . "\n");
  }
  
//  $link = odbc_connect($dsn, $db_user, $db_pass);
//  if (odbc_error()) {
//    mbTrace(odbc_errormsg(), "Could no connect");
//    die;
//  }

  mbTrace("Yes", "Connection successful");
  $chrono->start();
  
  $list = $db->getAll($sql);
  if (PEAR::isError($list)) {
      
      mbTrace($list->getMessage(), "Erreur");
      mbTrace($list->getDebugInfo(), "Debug Info");
      die();
  }
   
  
//  $res = odbc_exec($link, $sql);
//  while ($obj = odbc_fetch_object($res)) {
////    mbTrace($obj, "Found Object");
//  }

  $chrono->stop();
}

mbTrace($index, "How many identical selects");
mbTrace($chrono, "Chrono");

?>
