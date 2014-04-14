<?php

/**
 * RAD48 Delegated Handler
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CRAD48DelegatedHandler
 * RAD48 Delegated Handler
 */
class CRAD3DelegatedHandler extends CITIDelegatedHandler {
  static $handled        = array ("CConsultation");
  protected $profil      = "SWF";
  protected $message     = "ORM";
  protected $transaction = "RAD3";

  /**
   * Check if the object is handled
   *
   * @param CMbObject $mbObject mb object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * @see parent::onAfterStore()
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    /** @var CConsultation $consultation */
    $consultation = $mbObject;
    if (!$consultation->element_prescription_id && !$consultation->_old->element_prescription_id) {
      return false;
    }

    $element  = $consultation->element_prescription_id ?
                  $consultation->loadRefElementPrescription() : $consultation->_old->loadRefElementPrescription();
    $category = $element->loadRefCategory();

    if (!$category) {
      return false;
    }

    switch ($category->chapitre) {
      case "imagerie":
        $code = "O01";

        if ($consultation->_eai_initiateur_group_id ||
            !$this->isMessageSupported($this->transaction, $this->message, $code, $consultation->_receiver)
        ) {
          return;
        }

        $this->sendITI($this->profil, $this->transaction, $this->message, $code, $consultation);

        break;
      default:
        return false;
    }

    return true;
  }

  /**
   * @see parent::onBeforeDelete()
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    return true;
  }

  /**
   * @see parent::onAfterDelete()
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    $consultation = $mbObject;
    if (!$consultation->_old->element_prescription_id) {
      return false;
    }

    $element  = $consultation->_old->loadRefElementPrescription();
    $category = $element->loadRefCategory();

    if (!$category) {
      return false;
    }

    switch ($category->chapitre) {
      case "imagerie":
        $code = "O01";
        if ($consultation->_eai_initiateur_group_id ||
            !$this->isMessageSupported($this->transaction, $this->message, $code, $consultation->_receiver)
        ) {
          return;
        }

        $this->sendITI($this->profil, $this->transaction, $this->message, $code, $consultation);

        break;
      default:
        return false;
    }

    return true;
  }
}
