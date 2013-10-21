<?php 

/**
 * $Id$
 *  
 * @category cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

PHP_SAPI === "cli" or die;

global $argv;

$hostname      = '';
$username      = '';
$password      = '';
$db_name       = '';
$max_duration  = 0;
$help          = false;
$dry_run       = false;
$i             = 0;
$command = array_shift($argv);

foreach ($argv as $key => $arg) {
  switch ($arg) {
    case "-t":
      $max_duration = $argv[$key+1];
      unset($argv[$key+1]);
      break;

    case "-d":
      $dry_run = true;
      break;

    case "-h":
      $help = true;
      break;

    default:
      switch ($i) {
        case 0:
          $hostname = $arg;
          break;
        case 1:
          $username = $arg;
          break;
        case 2:
          $password = $arg;
          break;
        case 3:
          $db_name = $arg;
      }

      $i++;
  }
}

echo chr(27)."[1m--- Kill SQL slow queries (".date("l d F H:i:s").") ---".chr(27)."[0m"."\n";

if ($help) {
  echo "Usage : $command <hostname> <username> <password> <db_name> options
  <hostname> : host to connect
  <username> : username requesting
  <password> : password of the user
  <db_name>  : database to select
  Options :
    [ -t <max_duration> ] : select slow queries >= this value, default all the queries
    [ -d ]                : dry run\n";
  return;
}

try {
  $conn = mysql_connect($hostname, $username, $password);

  if (!$conn) {
    throw new Exception("Connection failed to $hostname");
  }

  $processes = array();

  $query = "SELECT *
           FROM INFORMATION_SCHEMA.PROCESSLIST
           WHERE `TIME` >= '" . addslashes($max_duration) . "'
            AND UPPER(SUBSTRING(`INFO`, 1, 6)) = 'SELECT'
            AND `DB` = '" . addslashes($db_name) . "'";

  $result = mysql_query($query, $conn);
  while ($row = mysql_fetch_assoc($result)) {
    $processes[] = $row;
  }

  if (!empty($processes)) {
    echo "=====\n" . date("d/m/Y H:i:s") . "\n";

    if ($dry_run) {
      echo "[Dry run]\n";
    }
  }

  foreach ($processes as $_process) {
    $kill_query = "KILL " . $_process["ID"];

    if (!$dry_run) {
      mysql_query($kill_query, $conn);
    }

    echo "\n# " . $_process["INFO"] . " # " . $_process["TIME"] . "s\n";
  }

  if (!empty($processes)) {
    echo "=====\n";
  }

  mysql_close($conn);
}
catch(Exception $e) {
  echo $e->getMessage();
}
