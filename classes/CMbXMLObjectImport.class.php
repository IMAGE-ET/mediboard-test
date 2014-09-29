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

abstract class CMbXMLObjectImport {
  /** @var CStoredObject[] */
  static $all = array();
  
  protected $filename;

  /** @var DOMDocument */
  protected $dom;

  /** @var array */
  protected $map = array();

  /** @var array */
  protected $options = array();
  
  /** @var DOMXPath */
  protected $xpath;
  
  protected $import_order = array(
    "//object",
  );

  /**
   * @param string $filename XML file name
   */
  function __construct($filename) {
    $this->filename  = $filename;

    $this->dom = new DOMDocument();
    $this->dom->load($this->filename);
    
    $this->xpath = new DOMXPath($this->dom);
  }
  
  function import($map, $options) {
    $this->map = $map;
    $this->options = $options;
    
    foreach ($this->import_order as $_xpath) {
      /** @var DOMNodeList|DOMElement[] $objects */
      $objects = $this->xpath->query($_xpath);

      foreach ($objects as $_object) {
        $this->importObject($_object);
      }
    }
    
    $this->afterImport();
  }

  /**
   * Post processing
   * 
   * @return void
   */
  function afterImport(){
    // Do nothing
  }

  /**
   * 
   * @param DOMElement $element
   *
   * @return CMbObject
   */
  function getObjectFromElement(DOMElement $element) {
    $class = $element->getAttribute("class");
    $object = new $class();
    
    $values = $this->getValuesFromElement($element);
    foreach ($values as $_field => $_value) {
      if ($_value && $object->_specs[$_field] instanceof CRefSpec && $_field !== $object->_spec->key) {
        /** @var DOMElement $_element */
        $_element = $this->xpath->query("//*[@id='$_value']")->item(0);
        $this->importObject($_element);
        
        if (isset($this->map[$_value])) {
          $values[$_field] = $this->getIdFromGuid($this->map[$_value]);
        }
      }
    }
    
    bindHashToObject($values, $object);
    
    return $object;
  }
  
  abstract function importObject(DOMElement $element);

  /**
   * Get ID from a GUID
   * 
   * @param string $guid The GUID
   *
   * @return int
   */
  function getIdFromGuid($guid) {
    list($class, $id) = explode("-", $guid);
    return $id;
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

