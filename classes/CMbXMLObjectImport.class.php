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

class CMbXMLObjectImport {
  /** @var CStoredObject[] */
  static $all = array();
  
  protected $filename;
  
  /** @var DOMDocument */
  protected $dom;
  
  /** @var DOMXPath */
  protected $xpath;

  /**
   * @param string $filename XML file name
   */
  function __construct($filename) {
    $this->filename  = $filename;

    $this->dom = new DOMDocument();
    $this->dom->load($this->filename);
    
    $this->xpath = new DOMXPath($this->dom);
  }
  
  function import($datamap) {
    /** @var DOMElement[] $objects */
    $objects = $this->xpath->query("//object");
    
    foreach ($objects as $_object) {
      $this->importObject($_object);
    }
    
    mbTrace(count(self::$all));
  }
  
  function importObject(DOMElement $element) {
    $guid = $element->getAttribute("id");
    
    self::$all[$guid] = $this->getValuesFromElement($element);
  }
  
  function getValuesFromElement(DOMElement $element) {
    /** @var DOMElement[] $value_elements */
    $value_elements = $this->xpath->query("field", $element);

    $values = array();
    foreach ($value_elements as $_element) {
      $values[$_element->getAttribute("name")] = utf8_decode($_element->nodeValue);
    }
    
    foreach ($element->attributes as $_attribute) {
      $_name = $_attribute->name;
      if (in_array($_name, array("id", "class"))) {
        continue;
      }
      
      /** @var DOMAttr $_attribute */
      $values[$_name] = $_attribute->value;
    }
    
    return $values;
  }

  function getSimilarFromElement(DOMElement $element) {
    $class = $element->getAttribute("class");

    $values = $this->getValuesFromElement($element);

    /** @var CStoredObject $object */
    $object = new $class;
    return $object->getSimilar($values);
  }

  function getNamedValueFromElement(DOMElement $element, $name) {
    $fields = $this->xpath->query("field[@name='$name']", $element);

    if ($fields->length == 0) {
      return null;
    }
    
    return utf8_decode($fields->item(0)->nodeValue);
  }

  /**
   * Get DOM elements by class name
   *
   * @param string $class Class name
   *
   * @return DOMNodeList|DOMElement[]
   */
  function getElementsbyClass($class) {
    return $this->xpath->query("//object[@class='$class']");
  }

  /**
   * Get DOM elements by class name
   *
   * @param string $class Class name
   *
   * @return DOMNodeList|DOMElement[]
   */
  function getElementsByFwdRef($class, $field_name, $field_value) {
    return $this->xpath->query("//object[@class='$class' and @$field_name='$field_value']");
  }
  
  function getObjectGuidByFwdRef(CMbObject $object, $fwd) {
    // Primary key
    if ($fwd === "id" || $fwd === $object->_spec->key) {
      return $object;
    }
    
    /** @var CRefSpec $spec */
    $spec = $object->_specs[$fwd]; // We assume it's always a CRefSpec
    
    $class = $spec->meta ? $object->{$spec->meta} : $spec->class;
    
    if (!$class) {
      return null;
    }
    
    $id = $object->$fwd;
    return "$class-$id";
  }
}

