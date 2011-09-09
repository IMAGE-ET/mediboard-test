<?php

/**
 * IHE Classes
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CIHE 
 * IHE classes
 */
class CIHE {
  static $object_handlers = array(
    "CSipObjectHandler" => "CITI30DelegatedHandler",
    "CSmpObjectHandler" => "CITI31DelegatedHandler",
  );
  
  static function getObjectHandlers() {
    return self::$object_handlers; 
  }
  
}
?>