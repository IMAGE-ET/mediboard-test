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
 * Enum value (list of values)
 */
class CEnumSpec extends CMbFieldSpec {
  public $list;
  public $typeEnum;
  public $vertical;
  public $columns;

  public $_list;
  public $_locales;

  /**
   * @see parent::__construct()
   */
  function __construct($className, $field, $prop = null, $aProperties = array()) {
    parent::__construct($className, $field, $prop, $aProperties);

    $this->_list = $this->getListValues($this->list);
    $this->_locales = array();

    foreach ($this->_list as $value) {
      $this->_locales[$value] = CAppUI::tr("$className.$field.$value");
    }
  }

  /**
   * Get the values of the list
   *
   * @param string $string The string to get the values of
   *
   * @return array
   */
  protected function getListValues($string){
    $list = array();

    if ($string !== "" && $string !== null) {
      $list = explode('|', $string);
    }

    return $list;
  }

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "enum";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec() {
    return "ENUM('".str_replace('|', "','", $this->list)."')";
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'list'     => 'list',
      'typeEnum' => array('radio', 'select'),
      'vertical' => 'bool',
      'columns'  => 'num',
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return CMbString::htmlSpecialChars(CAppUI::tr("$object->_class.$fieldName.$propValue"));
  }

  /**
   * @see parent::checkOptions()
   */
  function checkOptions(){
    parent::checkOptions();
    if (!$this->list) {
      CModelObject::warning("CEnumSpec-list-missing-for-field-fieldName%s-of-class-className%s", $this->fieldName, $this->className);
    }
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $value = $object->{$this->fieldName};
    $specFragments = $this->getListValues($this->list);
    if (!in_array($value, $specFragments)) {
      return "N'a pas une valeur possible";
    }

    return null;
  }

  /**
   * @see parent::sample()
   */
  function sample($object, $consistent = true){
    parent::sample($object, $consistent);
    $specFragments = $this->getListValues($this->list);
    $object->{$this->fieldName} = self::randomString($specFragments, 1);
  }

  /**
   * @see parent::regressionSamples()
   */
  function regressionSamples() {
    return $this->getListValues($this->list);;
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    $field         = CMbString::htmlSpecialChars($this->fieldName);
    $typeEnum      = CMbArray::extract($params, "typeEnum", $this->typeEnum ? $this->typeEnum : "select");
    $columns       = CMbArray::extract($params, "columns", $this->columns ? $this->columns : 1);
    $separator     = CMbArray::extract($params, "separator");
    $cycle         = CMbArray::extract($params, "cycle", 1);
    $alphabet      = CMbArray::extract($params, "alphabet", false);
    $form          = CMbArray::extract($params, "form"); // needs to be extracted

    // Empty label
    if ($emptyLabel = CMbArray::extract($params, "emptyLabel")) {
      $emptyLabel = CAppUI::tr($emptyLabel);
    }

    // Extra info por HTML generation
    $extra         = CMbArray::makeXmlAttributes($params);
    $locales       = $this->_locales;
    $className     = CMbString::htmlSpecialChars(trim("$className $this->prop"));
    $html          = "";

    // Alpha sorting
    if ($alphabet) {
      asort($locales);
    }

    // Turn readonly to disabled
    $readonly  = CMbArray::extract($params, "readonly");
    $disabled = $readonly ? "disabled=\"1\"" : "";

    switch ($typeEnum) {
      default:
      case "select":

        $html .= "<select name=\"$field\" class=\"$className\" $disabled $extra>";

        // Empty option label
        if ($emptyLabel) {
          $emptyLabel = "&mdash; $emptyLabel";

          if ($value === null) {
            $html .= "\n<option value=\"\" selected=\"selected\">$emptyLabel</option>";
          }
          else {
            $html .= "\n<option value=\"\">$emptyLabel</option>";
          }
        }

        // All other options
        foreach ($locales as $key => $item) {
          $selected = "";
          if (($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default" && !$emptyLabel)) {
            $selected = " selected=\"selected\"";
          }

          $html .= "\n<option value=\"$key\" $selected>$item</option>";
        }

        $html .= "\n</select>";
        return $html;

      case "radio":
        $compteur = 0;

        // Empty radio label
        if ($emptyLabel) {
          if ($value === null) {
            $html .= "\n<input type=\"radio\" name=\"$field\" value=\"\" checked=\"checked\" />";
          }
          else {
            $html .= "\n<input type=\"radio\" name=\"$field\" value=\"\" />";
          }
          $html .= "<label for=\"{$field}_\">$emptyLabel</label> ";
        }

        // All other radios
        foreach ($locales as $key => $item) {
          $selected = "";
          if (($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default")) {
            $selected = " checked=\"checked\"";
          }

          $html .= "\n<input type=\"radio\" name=\"$field\" value=\"$key\" $selected class=\"$className\" $disabled $extra />
                       <label for=\"{$field}_{$key}\">$item</label> ";
          $compteur++;

          $modulo = $compteur % $cycle;
          if ($separator != null && $modulo == 0 && $compteur < count($locales)) {
            $html  .= $separator;
          }

          if ($this->vertical) {
            $html .= "<br />\n";
          }
        }

        return $html;
    }
  }

  /**
   * @see parent::getLabelForAttribute()
   */
  function getLabelForAttribute($object, &$params){
    // to extract the XHTML invalid attribute "typeEnum"
    $typeEnum = CMbArray::extract($params, "typeEnum");
    return parent::getLabelForAttribute($object, $params);
  }

  /**
   * @see parent::getLitteralDescription()
   */
  function getLitteralDescription() {
    $litterals = array();
    foreach ($this->_list as $_list) {
      $litterals[] = "'$_list' (".CAppUI::tr($this->className.".".$this->fieldName.".".$_list).")";
    }
    return "Chaîne de caractère dont les valeurs possibles sont : ".implode(", ", $litterals).". ".
    parent::getLitteralDescription();
  }


}
