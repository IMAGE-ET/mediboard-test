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

class CCurrencySpec extends CFloatSpec {
  public $precise;

  function getSpecType() {
    return "currency";
  }

  function getOptions(){
    return array(
      'precise' => 'bool',
    ) + parent::getOptions();
  }

  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    $decimals = CMbArray::extract($params, "decimals", $this->decimals);
    $empty    = CMbArray::extract($params, "empty");
    return CSmartyMB::currency($value, $decimals, $this->precise, $empty);
  }

  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 6);
    return parent::getFormHtmlElement($object, $params, $value, $className).CAppUI::conf("currency_symbol");
  }

  function getDBSpec() {
    $size = $this->precise ? "12, 5" : "10, 3";
    return "DECIMAL ($size)".($this->pos ? " UNSIGNED" : "");
  }
}
