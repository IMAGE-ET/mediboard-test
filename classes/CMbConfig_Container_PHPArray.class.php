<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

@require_once "Config/Container/PHPArray.php";

class CMbConfig_Container_PHPArray extends Config_Container_PHPArray {
  /**
   * Methode reimplemented to remove the trailing "?>"
   *
   * @param string $datasrc Path to the config file
   * @param mixed  &$obj    Data to write into the file
   *
   * @return bool|object|string
   */
  function writeDatasrc($datasrc, &$obj) {
    $fp = @fopen($datasrc, 'w');
    if ($fp) {
      $string = "<?php\n". $this->toString($obj); // ."? >"; Does not write the closing tag
      $len = strlen($string);
      @flock($fp, LOCK_EX);
      @fwrite($fp, $string, $len);
      @flock($fp, LOCK_UN);
      @fclose($fp);
      return true;
    }
    else {
      return PEAR::raiseError('Cannot open datasource for writing.', 1, PEAR_ERROR_RETURN);
    }
  }
}