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
    $bg_reset = $reset_value ? "#$reset_value" : "transparent";

    $value = (!$value && ($this->notNull || $this->default)) ? $default_color : $value;

    $sHtml = "
    <input type=\"text\" class=\"color_picker\" name=\"$field\" value=\"$value\" $extra />
    <button type=\"button\" onclick=\"var elem = $(this).previous('input'); \$V(elem, '$reset_value', true); elem.setStyle({backgroundColor: '$bg_reset'});\" class='cancel notext'></button>
    ";
    if ($form && !$readonly) {
      $js_params = "{}";
      if (!$this->notNull) {
        $js_params = "{required:false}";
      }
      $sHtml .= "<script type=\"text/javascript\">
        Main.add(function(){
          var _e = getForm('".$form."').elements['".$field."'];
          new jscolor.color(_e, $js_params);
        })
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

  /**
   * return a font color following the hexa color given (background)
   *
   * @param $hex_value
   *
   * @return float
   */
  static function get_text_color($hex_value) {
    $hex = str_replace('#', '', $hex_value);
    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));
    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
  }

}