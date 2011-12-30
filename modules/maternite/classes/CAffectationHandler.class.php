<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CAffectationHandler extends CMbObjectHandler {
  static $handled = array ("CAffectation");
 
  static function isHandled(CMbObject $mbObject) {
    if(!CModule::getActive("dPhospi")){
      return false;
    }
    return in_array($mbObject->_class, self::$handled);
  }
  
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    foreach ($mbObject->loadRefsAffectationsEnfant() as $_affectation) {
      $_affectation->lit_id = $mbObject->lit_id;
      if ($msg = $_affectation->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
}