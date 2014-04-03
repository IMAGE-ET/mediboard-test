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
  static $handled = array ("CCompteRendu");

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
    return $this->requesthandler($object);

  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $object Object
   *
   * @return bool
   */
  function onAfterDelete(CMbObject $object) {
    if (!$this->isHandled($object)) {
      return false;
    }
    return $this->requesthandler($object, 'delete');
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
    $ds = CSQLDataSource::get("std");
    if (!$type) {
      $type = $object->_ref_current_log->type;
    }
    $date        = CMbDT::dateTime();
    $class       = $object->_class;
    $id          = $object->_id;

    switch ($object->_class) {
      case 'CCompteRendu':
        /** @var CCompteRendu $object */
        $object->completeField("author_id");
        $object->loadRefAuthor();
        $group       = $object->_ref_author->loadRefFunction()->loadRefGroup();
        break;

      default:
        return false;
    }
    if (!CAppUI::conf("search active_handler active_handler_search", $group)) {
      return false;
    }
    $query = "INSERT INTO `search_indexing` (`object_class`,`object_id`,`type` ,`date`)
        VALUES ('$class','$id', '$type', '$date')";
    $ds->exec($query);
    return true;
  }
}