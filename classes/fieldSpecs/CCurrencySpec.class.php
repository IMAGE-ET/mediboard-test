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
 * Currency value
 */
class CCurrencySpec extends CFloatSpec {
  public $precise;

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "currency";
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'precise' => 'bool',
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    $decimals = CMbArray::extract($params, "decimals", $this->decimals);
    $empty    = CMbArray::extract($params, "empty");
    return CSmartyMB::currency($value, $decimals, $this->precise, $empty);
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 6);
    return parent::getFormHtmlElement($object, $params, $value, $className).CAppUI::conf("currency_symbol");
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec() {
    $size = $this->precise ? "12, 5" : "10, 3";
    return "DECIMAL ($size)".($this->pos ? " UNSIGNED" : "");
  }
}
