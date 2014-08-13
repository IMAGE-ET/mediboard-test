<?php

/**
 * $Id: CBoolSpec.class.php 21050 2013-11-22 16:55:49Z mytto $
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 21050 $
 */

/**
 * Boolean value (0 or 1)
 */
class CColorSpec extends CMbFieldSpec {

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "color";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "VARCHAR (6)";   // hex value (FF0000)
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    $field        = CMbString::htmlSpecialChars($this->fieldName);
    $form         = CMbArray::extract($params, "form");
    $extra        = CMbArray::makeXmlAttributes($params);
    $readonly     = CMbArray::extract($params, "readonly");

    $default_color = $this->default ? $this->default : "ffffff";

    $reset_value = $this->notNull ? $default_color : "";

    $value = (!$value && ($this->notNull || $this->default)) ? $default_color : $value;

    $sHtml = "
    <input type=\"text\" class=\"color_picker\" name=\"$field\" value=\"$value\" $extra />
    <button type=\"button\" onclick=\"var elem = $(this).previous('input'); elem.value = '$reset_value'; elem.setStyle({backgroundColor: '#$reset_value'});\" class='cancel notext'></button>
    ";
    if ($form && !$readonly) {
      $js_params = "{}";
      if (!$this->notNull) {
        $js_params = "{required:false}";
      }
      $sHtml .= "<script type=\"text/javascript\">
        var _e = getForm('".$form."').elements['".$field."'];
        Main.add(function(){new jscolor.color(_e, $js_params)})
      </script>";
    }
    return $sHtml;
  }

  /**
   * @see parent::sample()
   */
  function sample($object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = sprintf( '#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255) );
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    if ($object->color) {
      return "<div style=\"background-color: #$object->color; min-width:30px; height:1em; border: solid 1px #afafaf;\"></div>";
    }
    return CAppUI::tr("Undefined");
  }

  /**
   * @see parent::getLitteralDescription()
   */
  function getLitteralDescription() {
    return "Code couleur hexadécimal #xxxxxx . ".
    parent::getLitteralDescription();
  }

}