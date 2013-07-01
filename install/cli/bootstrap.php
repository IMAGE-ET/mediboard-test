<?php
/**
 * CLI boostrap
 *
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

// CLI or die
PHP_SAPI === "cli" or die;

$mbpath = dirname(__FILE__)."/../../";

require_once $mbpath."classes/CMbArray.class.php";
require_once $mbpath."classes/CValue.class.php";
require_once $mbpath."classes/CMbPath.class.php";
require_once $mbpath."includes/mb_functions.php";
require_once $mbpath."includes/version.php";
require_once $mbpath."classes/Chronometer.class.php";

/**
 * CLI autoloader
 *
 * @param string $class_name Class to load
 *
 * @return bool
 */
function autoloader($class_name) {
  $dir = dirname(__FILE__)."/../classes";
  $file = "$dir/$class_name.class.php";

  if (file_exists($file)) {
    include $file;
  }

  return false;
}

spl_autoload_register("autoloader");

