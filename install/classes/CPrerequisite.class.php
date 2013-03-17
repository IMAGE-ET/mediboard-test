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
abstract class CPrerequisite extends CCheckable {
  var $name = "";
  var $description = "";
  var $mandatory = false;
  var $reasons = array();

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
}
