<?php 

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link       http://www.mediboard.org */

/**
 * Class CSearchObjectHandler
 */
class CSearchObjectHandler extends CMbObjectHandler {
  /**
   * @var array
   */
  static $handled = array ("CCompteRendu","CTransmissionMedicale","CObservationMedicale","CConsultation", "CConsultAnesth");

  /**
   * If object is handled ?
   *
   * @param CMbObject $object Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $object) {
    return in_array($object->_class, self::$handled);
  }
  /**
   * Trigger after event store
   *
   * @param CMbObject $object Object
   *
   * @return bool
   */
  function onAfterStore(CMbObject $object) {
    if (!$this->isHandled($object) && !parent::onAfterStore($object)) {
      return false;
    }
    return self::requesthandler($object);

  }
  /**
   * Trigger before event delete
   *
   * @param CMbObject $object Object
   *
   * @return bool
   */
  function onBeforeDelete(CMbObject $object) {
    if (!$this->isHandled($object)) {
      return false;
    }
    $object->_save_id = $object->_id;
    return true;
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $object Object
   *
   * @return bool
   */
  function onAfterDelete(CMbObject $object) {
    if (!$this->isHandled($object)) {
      return false;
    }
    $object->_id = $object->_save_id;
    return self::requesthandler($object, 'delete');
  }

  /**
   * function to call in static way in class.
   *
   * @param CMbObject $object the object you want to handled
   * @param string    $type   [optionnal]
   *
   * @return bool
   */
  static function requesthandler(CMbObject $object, $type = null) {
    if ((($object instanceof CConsultation) || ($object instanceof CConsultAnesth))  && !$object->sejour_id) {
      return false;
    }
    if (!$type) {
      $type = $object->_ref_current_log->type;
    }
    $search_indexing               = new CSearchIndexing();
    $search_indexing->type         = $type;
    $search_indexing->date         = CMbDT::dateTime();
    $search_indexing->object_class = $object->_class;
    $search_indexing->object_id    = $object->_id;

    switch ($object->_class) {
      case 'CCompteRendu':
        /** @var CCompteRendu $object */
        $object->completeField("author_id");
        $object->loadRefAuthor();
        $group       = $object->_ref_author->loadRefFunction()->loadRefGroup();
        break;
      case 'CConsultAnesth':
        /** @var CConsultAnesth $object */
        $object->loadRefChir();
        $group = $object->_ref_chir->loadRefFunction()->loadRefGroup();
        break;
      case 'CConsultation':
        /** @var CConsultation $object */
        $object->loadRefPraticien();
        $group = $object->_ref_praticien->loadRefFunction()->loadRefGroup();
        break;
      case 'CObservationMedicale':
      case 'CTransmissionMedicale':
        /** @var CTransmissionMedicale $object */
        $object->completeField("user_id");
        $object->loadRefUser();
        $group       = $object->_ref_user->loadRefFunction()->loadRefGroup();
        break;
      default:
        return false;
    }
    if (!CAppUI::conf("search active_handler active_handler_search", $group)) {
      return false;
    }
    $search_indexing->store();
    return true;
  }
}