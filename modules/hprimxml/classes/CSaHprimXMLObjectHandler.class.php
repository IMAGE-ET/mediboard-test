<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSaHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  static $handled = array ("CSejour", "COperation");

  static function isHandled(CMbObject &$mbObject) {
    return in_array($mbObject->_class_name, self::$handled);
  }

  function onAfterStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
    $receiver = $mbObject->_receiver;  
    
    
  }
}
?>