<?php

/**
 * $Id: $
 *
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Modification des affectations des bébés pour suivre le lit de la parturiente
 */

class CAffectationHandler extends CMbObjectHandler {
  static $handled = array ("CAffectation");

  /**
   * @see parent::isHandled()
   */
  static function isHandled(CMbObject $mbObject) {
    if (!CModule::getActive("dPhospi")) {
      return false;
    }
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * @see parent::onAfterStore()
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    /** @var $mbObject CAffectation */
    /** @var $_affectation CAffectation */
    foreach ($mbObject->loadRefsAffectationsEnfant() as $_affectation) {
      $_affectation->lit_id = $mbObject->lit_id;
      if ($msg = $_affectation->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
}