<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * DateTime value (YYYY-MM-DD HH:MM:SS)
 */
class CDateTimeSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "dateTime";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "DATETIME";
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    if ($smarty) {
      include_once $smarty->_get_plugin_filepath('modifier', 'date_format');
    }

    $propValue = $object->{$this->fieldName};

    $format = CMbArray::extract($params, "format", CAppUI::conf("datetime"));
    if ($format === "relative") {
      $relative = CMbDate::relative($propValue, CMbDT::dateTime());
      return $relative["count"] . " " . CAppUI::tr($relative["unit"] . ($relative["count"] > 1 ? "s" : ""));
    }

    $date = CMbArray::extract($params, "date");
    if ($date && CMbDT::date($propValue) == $date) {
      $format = CAppUI::conf("time");
    }

    return ($propValue && $propValue != "0000-00-00 00:00:00") ?
      smarty_modifier_date_format($propValue, $format) :
      "";
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = &$object->{$this->fieldName};

    if (!preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[ \+][0-9]{1,2}:[0-9]{1,2}(:[0-9]{1,2})?$/", $propValue)) {
      if ($propValue === 'current'|| $propValue ===  'now') {
        $propValue = CMbDT::dateTime();
        return null;
      } 
      return "format de dateTime invalide : '$propValue'";
    }

    $propValue = strtr($propValue, "+", " ");

    return null;
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);

    $object->{$this->fieldName} = "19".self::randomString(CMbFieldSpec::$nums, 2).
      "-".self::randomString(CMbFieldSpec::$months, 1).
      "-".self::randomString(CMbFieldSpec::$days, 1).
      " ".self::randomString(CMbFieldSpec::$hours, 1).
      ":".self::randomString(CMbFieldSpec::$mins, 1).
      ":".self::randomString(CMbFieldSpec::$mins, 1);
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementDateTime($object, $params, $value, $className, CAppUI::conf("datetime"));
  }
}
