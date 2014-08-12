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
 * Long string
 */
class CTextSpec extends CMbFieldSpec {
  public $markdown;

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "text";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "TEXT";
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object) {
    return null;
  }

  /**
   * @see parent::getHtmlValue()
   */
  function getHtmlValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};

    // Empty value: no paragraph
    if (!$value) {
      return "";
    }

    // Truncate case: no breakers but inline bullets instead
    if ($truncate = CValue::read($params, "truncate")) {
      $value = CMbString::truncate($value, $truncate === true ? null : $truncate);
      $value = CMbString::nl2bull($value);
      return CMbString::htmlSpecialChars($value);
    }

    // Markdown case: full delegation
    if ($this->markdown) {
      // In order to prevent from double escaping
      $content = CMbString::markdown(html_entity_decode($value));
      return "<div class='markdown'>$content</div>";
    }

    // Standard case: breakers and paragraph enhancers
    $text = "";
    $value = str_replace(array("\r\n", "\r"), "\n", $value);
    $paragraphs = preg_split("/\n{2,}/", $value);
    foreach($paragraphs as $_paragraph) {
      if (!empty($_paragraph)) {
        $_paragraph = nl2br(CMbString::htmlSpecialChars($_paragraph));
        $text .= "<p>$_paragraph</p>";
      }
    }
    return $text;
  }

  /**
   * @see parent::sample()
   */
  function sample($object, $consistent = true) {
    parent::sample($object, $consistent);
    $chars = array_merge(CMbFieldSpec::$chars, array(' ', ' ', ', ', '. '));
    $object->{$this->fieldName} = self::randomString($chars, 200);
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className) {
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }

  /**
   * @see parent::filter()
   */
  function filter($value) {
    if (CAppUI::conf("purify_text_input")) {
      $value = CMbString::purifyHTML($value);
    }
    return parent::filter($value);
  }

  /**
   * @see parent::getLitteralDescription()
   */
  function getLitteralDescription() {
    return "Texte long. ".
      parent::getLitteralDescription();
  }
}
