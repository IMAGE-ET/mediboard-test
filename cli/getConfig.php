<?php
/**
 * Get configuration value
 *
 * Precedence order:
 *  config_dist.php < config.php < DB config < config_overload.php
 *
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

// CLI or die
PHP_SAPI === "cli" or die;

if (count($argv) <= 1) {
  $command = array_shift($argv);

  echo "Usage : $command <key>\n
    <key> : config path, separated by spaces\n";
  return;
}

// Config key, from args
$key = $argv[1];

global $dPconfig;
require __DIR__."/../includes/config_all.php";

/**
 * Get configuration value from a path and a tree of configuration values
 *
 * @param string $path Config path
 * @param array  $conf Config tree
 *
 * @return null|string
 */
function getConf($path, $conf) {
  if (!$path) {
    return $conf;
  }

  $items = explode(' ', $path);
  foreach ($items as $part) {
    // dP ugly hack
    if (!array_key_exists($part, $conf) && array_key_exists("dP$part", $conf)) {
      $part = "dP$part";
    }

    if (!$conf[$part]) {
      return null;
    }

    $conf = $conf[$part];
  }

  return $conf;
}

/**
 * Get overloaded configuration value
 *
 * @param string $path Config path
 *
 * @return null|string
 */
function getOverloadConf($path) {
  include __DIR__."/../includes/config_overload.php";

  if (empty($dPconfig)) {
    return null;
  }

  return getConf($path, $dPconfig);
}

// Check if config can be in DB
$config_db = getConf("config_db", $dPconfig);

// Fetch config from DB
if ($config_db) {
  // If config not overridden by config overload, wee need to get it from DB
  $overload_conf = getOverloadConf($key);

  if ($overload_conf === null) {
    $std = getConf("db std", $dPconfig);

    $dbname = $std["dbname"];
    $dbhost = $std["dbhost"];
    $dbuser = $std["dbuser"];
    $dbpass = $std["dbpass"];

    $pdo = new PDO("mysql:dbname=$dbname;host=$dbhost", $dbuser, $dbpass);
    $stmt = $pdo->prepare("SELECT `value` FROM `config_db` WHERE `key` = ?;");
    $stmt->execute(array($key));

    $value = $stmt->fetchColumn();

    if ($value !== false) {
      echo $value;
      return;
    }
  }
}

echo getConf($key, $dPconfig);
