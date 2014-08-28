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
 * Object exporting utility class
 */
class CMbObjectExport {
  const DEFAULT_DEPTH = 6;
  
  /** @var CMbXMLDocument */
  public $doc;
  
  /** @var CMbObject */
  public $object;
  
  /** @var array */
  public $backrefs_tree;
  
  /** @var array */
  public $fwdrefs_tree;
  
  /** @var integer */
  public $depth = self::DEFAULT_DEPTH;
  
  /** @var bool */
  public $empty_values = true;

  /**
   * Trim no break space and 0xFF chars
   * 
   * @param string $s String to trim
   *
   * @return string
   */
  static function trimString($s) {
    return trim(trim($s), "\xA0\xFF");
  }

  /**
   * Export constructor
   *
   * @param CMbObject $object        Object to export
   * @param null      $backrefs_tree Backrefs tree
   *
   * @throws CMbException
   */
  function __construct(CMbObject $object, $backrefs_tree = null) {
    if (!$object->getPerm(PERM_READ)) {
      throw new CMbException("Permission denied");
    }
    
    $this->object = $object;
    $this->backrefs_tree = isset($backrefs_tree) ? $backrefs_tree : $object->getExportedBackRefs();
  }

  /**
   * Set the forward refs tree to export
   * 
   * @param array $fwdrefs_tree Forward refs tree to export
   * 
   * @return void
   */
  function setForwardRefsTree($fwdrefs_tree) {
    $this->fwdrefs_tree = $fwdrefs_tree;
  }
  
  /**
   * Export to DOM
   * 
   * @return CMbXMLDocument
   */
  function toDOM(){
    $this->doc = new CMbXMLDocument("utf-8");
    $this->doc->formatOutput = true;
    $root = $this->doc->createElement("mediboard-export");
    $root->setAttribute("date", CMbDT::dateTime());
    $root->setAttribute("root", $this->object->_guid);
    $this->doc->appendChild($root);
    
    $this->_toDOM($this->object, $this->depth);
    
    return $this->doc;
  }

  /**
   * Internal DOM export method
   *
   * @param CStoredObject $object Object to export
   * @param int           $depth  Export depth
   *
   * @return void
   */
  private function _toDOM(CStoredObject $object, $depth) {
    if ($depth == 0 || !$object->_id || !$object->getPerm(PERM_READ)) {
      return;
    }
    
    $doc = $this->doc;
    $object_node = $doc->getElementById($object->_guid);
    
    // Objet deja exporté
    if ($object_node) {
      return;
    }
    
    $object_node = $doc->createElement("object");
    $object_node->setAttribute('class', $object->_class);
    $object_node->setAttribute('id', $object->_guid);
    $object_node->setIdAttribute('id', true);
    $doc->documentElement->appendChild($object_node);
    
    $db_fields = $object->getPlainFields();
    
    foreach ($db_fields as $key => $value) {
      // Forward Refs Fields
      $_fwd_spec = $object->_specs[$key];
      if ($_fwd_spec instanceof CRefSpec) {
        if ($key === $object->_spec->key) {
          continue;
        }

        if (!isset($this->fwdrefs_tree[$object->_class]) || !in_array($key, $this->fwdrefs_tree[$object->_class])) {
          continue;
        }
        
        $object->loadFwdRef($key);
        $guid = "";
        $_object = $object->_fwd[$key];
        
        if ($_object && $_object->_id) {
          if ($key !== $object->_spec->key) {
            $this->_toDOM($_object, $depth-1);
          }

          $guid = $_object->_guid;
        }
        
        if ($this->empty_values || $guid) {
          $object_node->setAttribute($key, $guid);
          //$doc->insertTextElement($object_node, "field", $id, array("name" => $key));
        }
      }
      
      // Scalar fields
      else {
        $value = self::trimString($value);
        
        if ($this->empty_values || $value !== "") {
          $doc->insertTextElement($object_node, "field", $value, array("name" => $key));
        }
      }
    }
    
    // Collections
    if (!isset($this->backrefs_tree[$object->_class])) {
      return;
    }
    
    foreach ($object->_backProps as $backName => $backProp) {
      if (!in_array($backName, $this->backrefs_tree[$object->_class])) {
        continue;
      }
      
      $object->makeBackSpec($backName);
      $objects = $object->loadBackRefs($backName);
      
      foreach ($objects as $_object) {
        $this->_toDOM($_object, $depth-1);
      }
    }
  }

  /**
   * Stream in text/xml mimetype
   * 
   * @return void
   */
  function streamXML(){
    $this->stream("text/xml");
  }

  /**
   * Stream in text/plain mimetype
   *
   * @return void
   */
  function streamXMLText(){
    $this->stream("text/plain");
  }

  /**
   * Stream the DOM
   * 
   * @param string $mimetype Mime type type
   * 
   * @return void
   */
  function stream($mimetype){
    $xml = $this->toDOM()->saveXML();
    $date = CMbDT::dateTime();
    
    header("Content-Type: $mimetype");
    header("Content-Disposition: attachment;filename=\"{$this->object} - $date.xml\"");
    header("Content-Length: ".strlen($xml));
    
    echo $xml;
  }
}
