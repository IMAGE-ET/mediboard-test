<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sms
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSmsObjectHandler extends CEAIObjectHandler {
  static $handled = array ("CProductDelivery", "CProductDeliveryTrace", "CAdministration");

  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!parent::onBeforeMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onBeforeMerge", $mbObject);
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!parent::onAfterMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterMerge", $mbObject);
  }
  
  function onAfterDelete(CMbObject $mbObject) {
    if (!parent::onAfterDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterDelete", $mbObject);
  }
}
?>