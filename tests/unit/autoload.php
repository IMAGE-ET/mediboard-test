<?php
/**
 * Autoload strategies
 *
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Tests
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id: autoload.php $
 * @link       http://www.mediboard.org
 */


/**
 * Class CMbTestAutoloader
 */
class CMbTestAutoloader {

  /**
   * Register the autoloader
   *
   * @return void
   */
  static function register() {
    spl_autoload_register(array(__CLASS__, 'autoload'));
  }

  /**
   * Test specific autoloader
   *
   * @param string $class Class to be loaded
   *
   * @return void
   */
  static function autoload($class) {

    $dirs = array(
      "classes/$class.class.php",
      "classes/*/$class.class.php",
      "mobile/*/$class.class.php",
      "modules/*/classes/$class.class.php",
      "modules/*/classes/*/$class.class.php",
      "modules/*/classes/*/*/$class.class.php",
      "install/classes/$class.class.php",
      "modules/*/tests/$class.php"
    );

    foreach ($dirs as $_dir) {
      $files = glob(__DIR__ . "/../../$_dir");
      foreach ($files as $filename) {
        include_once $filename;

      }
    }
  }
}
