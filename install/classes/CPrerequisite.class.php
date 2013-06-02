<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

/**
 * Prerequisite abstract class
 */
abstract class CPrerequisite {
  public $name = "";
  public $description = "";
  public $mandatory = false;
  public $reasons = array();

  /**
   * Check prerequisite
   *
   * @param bool $strict Check also warnings
   *
   * @return bool
   */
  abstract function check($strict = true);

  /**
   * Return all instances of self
   *
   * @return self[]
   */
  abstract function getAll();

  /**
   * Check all items
   *
   * @param bool $strict Make strict checking
   *
   * @return bool
   */
  function checkAll($strict = true){
    foreach ($this->getAll() as $item) {
      if (!$item->check($strict)) {
        return false;
      }
    }

    return true;
  }
}
