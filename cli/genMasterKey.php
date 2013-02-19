<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

// CLI or die
PHP_SAPI === "cli" or die;

require_once "utils.php";
require_once dirname(__FILE__)."/classes/Procedure.class.php";

include(dirname(dirname(__FILE__))."/lib/phpseclib/phpseclib/Crypt/Random.php");

function genMasterKey($dirKey, $apacheGroup = "www-data") {

  if ($apacheGroup === "") {
    $apacheGroup = "www-data";
  }

  $dirKey = rtrim($dirKey, "/");

  $dirs = explode("/", $dirKey);

  chdir("/");
  foreach ($dirs as $dir) {
    if ($dir === "") {
      continue;
    }

    if (!file_exists($dir)) {
      mkdir($dir, 0750);
      chgrp($dir, $apacheGroup);
    }

    if (!is_readable($dir)) {
      chmod($dir, 0750);
      chgrp($dir, $apacheGroup);
    }

    chdir($dir);
  }

  $f = fopen($dirKey."/.mediboard.key", "w");
  if (!$f) {
    echo "Failed to create key file!";
    return 0;
  }
  fclose($f);

  chmod(".mediboard.key", 0760);
  chgrp(".mediboard.key", $apacheGroup);

  $keyA = bin2hex(crypt_random_string(16));
  $keyB = bin2hex(crypt_random_string(16));

  $handle = fopen(".mediboard.key", "w");

  if (!$handle) {
    return 0;
  }

  fwrite($handle, $keyA."\n".$keyB);
  fclose($handle);

  chmod(".mediboard.key", 0750);
  return 1;
}

/**
 * The Procedure for the genMasterKey function
 *
 * @param Menu $backMenu The Menu for return
 *
 * @return void
 */
function genMasterKeyProcedure($backMenu) {
  $procedure = new Procedure();

  $choice = "0";
  $procedure->showReturnChoice($choice);

  $qt_dirKey  = $procedure->createQuestion("\nEnter the directory to create in a recursively way (ie /var/.mediboard/): ");
  $dirKey     = $procedure->askQuestion($qt_dirKey);

  if ( $dirKey === $choice ) {
    $procedure->clearScreen();
    $procedure->showMenu($backMenu, true);
    exit();
  }

  $qt_apacheGrp    = $procedure->createQuestion("\nApache user's group [default www-data]: ", "www-data");
  $apacheGrp       = $procedure->askQuestion($qt_apacheGrp);

  echo "\n";
  genMasterKey($dirKey, $apacheGrp);
}

/**
 * Function to use genMasterKey in one line
 *
 * @param string $command The command input
 * @param array  $argv    The given parameters
 *
 * @return bool
 */
function genMasterKeyCall($command, $argv) {
  if (count($argv) == 2) {
    $dirKey     = $argv[0];
    $apacheGrp  = $argv[1];

    genMasterKey($dirKey, $apacheGrp);

    return 0;
  }
  else {
    echo "\nUsage : $command genMasterKey <key directory> [<apache group>]\n
<key directory>              : directory where to create the Mediboard's key\n
Options :
[<apache group>]       : Apache group, default www-data\n\n";

    return 1;
  }
}

// To use genMasterKey without menu_v2.php
global $argv;

$dirKey    = "";
$apacheGrp = "www-data";
$help      = false;
$command = array_shift($argv);

foreach ($argv as $key => $arg) {
  switch ($arg) {
    case "-d":
      $dirKey = $argv[$key+1];
      unset($argv[$key+1]);
      break;
    case "-g":
      $apacheGrp = $argv[$key+1];
      unset($argv[$key+1]);
      break;
    case "-h":
      $help = true;
      break;
  }
}

if ($help) {
  echo "\nUsage : $command -d <key directory> options
<key directory>     : directory where to create the Mediboard's key
Options :
[-g <apache group>] : Apache group, default www-data\n\n";
  return;
}

genMasterKey($dirKey, $apacheGrp);