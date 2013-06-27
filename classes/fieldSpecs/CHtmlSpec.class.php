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
 * HTML string
 */
class CHtmlSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "html";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "MEDIUMTEXT";
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    return $object->{$this->fieldName};
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
    
    // Root node surrounding
    $source = utf8_encode("<div>$propValue</div>");

    //for external html content => no validation
    if (stripos($source, "<html") !== false) {
      return null;
    }

    // Entity purge
    $source = preg_replace("/&\w+;/i", "", $source);

    // Escape warnings, returns false if really invalid
    if (!@DOMDocument::loadXML($source)) {
      trigger_error("Error: Html document bad formatted", E_USER_WARNING);
      return "Le document HTML est mal formé, ou la requête n'a pas pu se terminer.";
    }

    return null;
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = <<<EOD
<h1>Titre 1</h1>
<p>Paragraphe</p>
<ul>
  <li>Item 1</li>
  <li>Item 2</li>
  <li>Item 3</li>
</ul>
EOD;
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
}
