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
  static $handled = array ();

  /**
   * Check the types which are handled.
   *
   * @param CMbObject $object the object
   *
   * @return bool
   */
  static function checkHandled($object) {

    $group = self::loadRefGroup($object);

    if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
      self::$handled = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
    }
    return true;
  }


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
    $this->checkHandled($object);
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
    $this->checkHandled($object);
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
    $this->checkHandled($object);

    if (!$this->isHandled($object)) {
      return false;
    }

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
    self::checkHandled($object);
    if ($object instanceof CCompteRendu && !$object->object_id) {
      return false;
    }

    if (!$type) {
      if (!$object->_ref_current_log) {
        $type = "create";
      }
      else {
        $type = $object->_ref_current_log->type;
      }
    }

    $search_indexing               = new CSearchIndexing();
    $search_indexing->type         = $type;
    $search_indexing->date         = CMbDT::dateTime();
    $search_indexing->object_class = $object->_class;
    $search_indexing->object_id    = ($object->_id) ? $object->_id : $object->_save_id ;

    $group = self::loadRefGroup($object);
    if (!CAppUI::conf("search active_handler active_handler_search", $group)) {
      return false;
    }
    $search_indexing->store();
    return true;
  }

  /**
   * Load Group from CMbObject
   *
   * @param CMbObject $object CMbObject
   *
   * @return CGroups
   */
  function loadRefGroup ($object) {
    switch ($object->_class) {
      case 'CCompteRendu':
        /** @var CCompteRendu $object */
        $object->completeField("author_id");
        $object->loadRefAuthor();
        $group       = $object->_ref_author->loadRefFunction()->loadRefGroup();
        break;
      case 'CConsultAnesth':
      case 'COperation':
        /** @var CConsultAnesth $object */
        $object->loadRefChir();
        $group = $object->_ref_chir->loadRefFunction()->loadRefGroup();
        break;
      case 'CConsultation':
      case 'CPrescriptionLineMedicament':
      case 'CPrescriptionLineMix':
      case 'CPrescriptionLineElement':
        $object->loadRefPraticien();
        $group = $object->_ref_praticien->loadRefFunction()->loadRefGroup();
        break;
      case 'CObservationMedicale':
      case 'CTransmissionMedicale':
        $object->completeField("user_id");
        $object->loadRefUser();
        $group       = $object->_ref_user->loadRefFunction()->loadRefGroup();
        break;
      case 'CFile':
        /** @var CFile $object */
        $object->completeField("author_id");
        $object->loadRefAuthor();
        $group       = $object->_ref_author->loadRefFunction()->loadRefGroup();
        break;
      default:
        if ($object->_class instanceof CExObject) {
          $group = $object->loadRefGroup();
        }
        else {
          return new CGroups();
        }

    }
    return $group;
  }
}