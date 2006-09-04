<?php 

global $AppUI, $m;

require_once($Appui->getSystemClass("mbconfig"));

$dPconfig['dPinterop']['sante400']['site_base'] = 'PICLIN927';

$mbConfig = new CMbConfig;
$mbConfig->update($dPconfig);
$mbConfig->load();

die();



$etablissements = array(
  "310" => "St Louis",
  "474" => "Sauvegarde",
  "927" => "clinique du Tonkin",
);

require_once "DB.php";

$chrono = new Chronometer();

$db_user = "GMB";
$db_pass = "GMB";

$sql = "SELECT * FROM PICLIN310.MDP01";

$chrono->start();

switch ($mode = mbGetValueFromGet("mode", "pear")) {
  /**
   * Mode PEAR::DB
   */
	case "pear":
  $dsn = array (
      "phptype"  => "odbc",
      "username" => "GMB",
      "password" => "GMB",
      "hostspec" => "sante400",
      "database" => "",
  );
  
  $chrono->start();

  // Create a new DB connection object and connect to the ODBC database.
  $db =& DB::connect($dsn);
  if (DB::isError($db)) {
      $error = "Unable to connect to DSN '$dsn'";
      $error .= "\nError: " . $db->getMessage();
      $error .= "\nDebug: " . $db->getDebugInfo();
      trigger_error(nl2br($error), E_USER_ERROR);
  }
    
  // Execute Query
  $db->setFetchMode(DB_FETCHMODE_ASSOC);
  $praticien = $db->getRow($sql);
  if (DB::isError($praticien)) {
      $error = "Unable to execute query '$sql'";
      $error .= "\nError: " . $praticien->getMessage();
      $error .= "\nDebug: " . $praticien->getDebugInfo();
      trigger_error(nl2br($error), E_USER_WARNING);
  }
 
  mbTrace($praticien, "Premier praticien trouvé");
		
	break;

  /**
   * Mode ODBC
   */
  case "odbc":
  $dsn = "sante400";


    // Connect to the ODBC database.
    $link = odbc_connect($dsn, $db_user, $db_pass);
    if (odbc_error()) {
      mbTrace(odbc_errormsg(), "Could no connect");
      die;
    }

    mbTrace("Yes", "Connection successful");
    
    // Execute Query
    $res = odbc_exec($link, $sql);
    while ($obj = odbc_fetch_object($res)) {
      mbTrace($obj, "Found Object");
    }
  
  $chrono->stop();
  
  break;

	default:
  mbTrace($mode, "mode not available");
	break;
}

$chrono->stop();

mbTrace($chrono, "Chrono");

?>
