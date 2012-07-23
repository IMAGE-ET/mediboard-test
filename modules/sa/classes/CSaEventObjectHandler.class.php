<?php

/**
 * SA Event Handler
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSaEventObjectHandler
 * SA Event Handler
 */

class CSaEventObjectHandler extends CEAIObjectHandler {
  static $handled = array ("COperation");

  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  
  /**
   * @see parent::onAfterStore()
   */  
  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
  }
  
  /**
   * @see parent::onBeforeDelete()
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!parent::onBeforeDelete($mbObject)) {
      return false;
    }
    
    $this->sendFormatAction("onBeforeDelete", $mbObject);
  }  
  
  /**
   * @see parent::onAfterDelete()
   */  
  function onAfterDelete(CMbObject $mbObject) {
    if (!parent::onAfterDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterDelete", $mbObject);
  }
}
?>
