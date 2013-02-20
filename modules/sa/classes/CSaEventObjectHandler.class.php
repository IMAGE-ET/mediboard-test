<?php

/**
 * SA Event Handler
 *  
 * @category SA
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
  /**
   * @var array
   */
  static $handled = array ("COperation", "CConsultation");

  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!parent::onBeforeDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onBeforeDelete", $mbObject);
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */  
  function onAfterDelete(CMbObject $mbObject) {
    if (!parent::onAfterDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterDelete", $mbObject);
  }
}