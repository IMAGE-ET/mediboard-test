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
 * Abstract CMbObject import class, using XML files
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
   * XML based import constructor
   *
   * @param string $filename XML file name
   *
   * @throws CMbException
   */
  function __construct($filename) {
    if (!is_readable($filename)) {
      throw new CMbException("File '$filename' is not readable");
    }

    $this->filename  = $filename;

    $this->dom = new DOMDocument();
    $this->dom->load($this->filename);
    
    $this->xpath = new DOMXPath($this->dom);
  }

  /**
   * Import an object from a DOM element
   *
   * @param array $map     Map information
   * @param array $options Options
   *
   * @return void
   */
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
   * Build an object from a DOM element
   *
   * @param DOMElement $element The DOM element
   *
   * @return CMbObject
   */
  function getObjectFromElement(DOMElement $element) {
    $class = $element->getAttribute("class");
    $object = new $class();
    
    $values = self::getValuesFromElement($element);
    foreach ($values as $_field => $_value) {
      if ($_value && $object->_specs[$_field] instanceof CRefSpec && $_field !== $object->_spec->key) {
        /** @var DOMElement $_element */
        $_element = $this->xpath->query("//*[@id='$_value']")->item(0);
        $this->importObject($_element);
        
        if (isset($this->map[$_value])) {
          $values[$_field] = self::getIdFromGuid($this->map[$_value]);
        }
      }
    }
    
    bindHashToObject($values, $object);
    
    return $object;
  }

  /**
   * Import an object from an XML element
   *
   * @param DOMElement $element The XML element to import the object from
   *
   * @return mixed
   */
  abstract function importObject(DOMElement $element);

  /**
   * Get ID from a GUID
   * 
   * @param string $guid The GUID
   *
   * @return int
   */
  static function getIdFromGuid($guid) {
    list($class, $id) = explode("-", $guid);
    return $id;
  }

  /**
   * Get an associative array of the raw values from the DOM element
   *
   * @param DOMElement $element The DOM element
   *
   * @return array
   */
  static function getValuesFromElement(DOMElement $element) {
    $values = array();
    /** @var DOMElement $_element */
    foreach ($element->childNodes as $_element) {
      if ($_element->nodeType !== XML_ELEMENT_NODE || $_element->nodeName !== "field") {
        continue;
      }

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

  /**
   * Get similar object from a DOM element
   *
   * @param DOMElement $element The DOM element
   * @param array      $fields  The fields to search on
   *
   * @return CStoredObject[]|null
   */
  function getSimilarFromElement(DOMElement $element, $fields = array()) {
    $class = $element->getAttribute("class");

    $values = self::getValuesFromElement($element);

    /** @var CStoredObject $object */
    $object = new $class;
    if (!empty($fields)) {
      $object->_spec->uniques = array($fields);
    }
    return $object->getSimilar($values);
  }

  /**
   * Get a value from an element
   *
   * @param DOMElement $element DOM element
   * @param string     $name    Field name
   *
   * @return null|string
   */
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
   * Get DOM elements by class name / field name / field value
   *
   * @param string $class       Class name
   * @param string $field_name  Field name
   * @param string $field_value Field value
   *
   * @return DOMElement[]|DOMNodeList
   */
  function getElementsByFwdRef($class, $field_name, $field_value) {
    return $this->xpath->query("//object[@class='$class' and @$field_name='$field_value']");
  }

  /**
   * Get an object guid of an object from a fwd ref name
   *
   * @param CMbObject $object Ovject
   * @param string    $fwd    Fwd ref name
   *
   * @return CMbObject|null|string
   */
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

  /**
   * Get objects list
   *
   * @param string $class         Class name
   * @param string $compare_field Search and view field
   * @param bool   $load_all      Load all objects from the current group
   * @param bool   $allow_create  Allow object creation
   *
   * @return array
   */
  function getObjectsList($class, $compare_field, $load_all = true, $allow_create = true) {
    $elements  = $this->getElementsbyClass($class);

    /** @var CMbObject $object */
    $object = new $class();
    $ds = $object->getDS();

    /** @var CMbObject[] $all_objects */
    $all_objects = array();
    if ($load_all) {
      $all_objects = $object->loadGroupList(null, $compare_field);
    }

    $objects = array();
    foreach ($elements as $_element) {
      $_id = $_element->getAttribute("id");

      $_values = CMbXMLObjectImport::getValuesFromElement($_element);

      /** @var CMbObject[] $_similar */
      $where = array(
        $compare_field => $ds->prepare("=?", $_values[$compare_field]),
      );
      $_similar = $object->loadGroupList($where);

      $objects[$_id] = array(
        "values"   => $_values,
        "similar"  => $_similar,
      );
    }

    $sortfunc = function ($a, $b) use ($compare_field) {
      return strcasecmp($a["values"][$compare_field], $b["values"][$compare_field]);
    };
    uasort($objects, $sortfunc);

    return array(
      "all_objects"  => $all_objects,
      "objects"      => $objects,
      "class"        => $class,
      "field"        => $compare_field,
      "allow_create" => $allow_create,
    );
  }
}

