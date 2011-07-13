<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12588 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHprimXML {
  static $object_handlers = array(
    "CSipHprimXMLObjectHandler",
    "CSmpHprimXMLObjectHandler",
    "CSaHprimXMLObjectHandler"
  );
  
  static function getObjectHandlers() {
    return self::$object_handlers; 
  }
  
}

?>