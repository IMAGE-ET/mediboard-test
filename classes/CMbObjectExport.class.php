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

class CMbObjectExport {
  const DEFAULT_DEPTH = 6;
  
  /**
   * @var CMbObject
   */
  var $dom = null;
  
  /**
   * @var CMbObject
   */
  var $object = null;
  
  /**
   * @var array
   */
  var $backrefs_tree = null;
  
  /**
   * @var integer
   */
  var $depth = self::DEFAULT_DEPTH;
  
  /**
   * @var bool
   */
  var $empty_values = true;
  
  static function str_trim($s) {
    return trim(trim($s), "\xA0\xFF");
  }
  
  function __construct(CMbObject $object, $backrefs_tree = null) {
    if (!$object->getPerm(PERM_READ)) {
      throw new CMbException("Permission denied");
    }
    
    $this->object = $object;
    $this->backrefs_tree = isset($backrefs_tree) ? $backrefs_tree : $object->getExportedBackRefs();
  }
  
  /**
   * @return CMbXMLDocument
   */
  function toDOM(){
    $this->doc = new CMbXMLDocument();
    $this->doc->formatOutput = true;
    $root = $this->doc->createElement($this->object->_guid);
    $root->setAttribute("date", mbDateTime());
    $this->doc->appendChild($root);
    
    $this->_toDOM($this->object, $this->depth);
    
    return $this->doc;
  }
  
  private function _toDOM(CMbObject $object, $depth) {
    if (!$depth || !$object->_id || !$object->getPerm(PERM_READ)) return;
    
    $doc = $this->doc;
    $object_node = $doc->getElementById($object->_guid);
    
    // Objet deja exporté
    if ($object_node) return;
    
    $object_node = $doc->createElement($object->_class);
    $object_node->setAttribute('id', $object->_guid);
    $object_node->setIdAttribute('id', true);
    $doc->documentElement->appendChild($object_node);
    
    $db_fields = $object->getPlainFields();
    
    foreach($db_fields as $key => $value) {
      // Forward Refs Fields
      if ($object->_specs[$key] instanceof CRefSpec) {
        $object->loadFwdRef($key);
        $guid = "";
        $_object = $object->_fwd[$key];
        
        if ($_object && $_object->_id) {
          if ($key !== $object->_spec->key) {
            $this->_toDOM($_object, $depth-1);
          }
          
          $guid = $_object->_guid;
        }
        
        if ($guid === "" || $guid === null) {
          continue;
        }
        
        $object_node->setAttribute($key, $guid);
      }
      
      // Scalar fields
      else {
        $value = self::str_trim($value);
        
        if ($this->empty_values || $value !== "") {
          $doc->insertTextElement($object_node, $key, $value);
        }
      }
    }
    
    // Collections
    if (!isset($this->backrefs_tree[$object->_class])) return;
    
    foreach($object->_backProps as $backName => $backProp) {
      if (!in_array($backName, $this->backrefs_tree[$object->_class])) continue;
      
      $object->makeBackSpec($backName);
      $objects = $object->loadBackRefs($backName);
      
      foreach($objects as $_object) {
        $this->_toDOM($_object, $depth-1);
      }
    }
  }
  
  function streamXML(){
    $this->stream("text/xml");
  }
  
  function streamXMLText(){
    $this->stream("text/plain");
  }
  
  function stream($mimetype){
    $xml = $this->toDOM()->saveXML();
    $date = mbDateTime();
    
    header("Content-Type: $mimetype");
    header("Content-Disposition: attachment;filename=\"{$this->object->_guid} - $date.xml\"");
    header("Content-Length: ".strlen($xml));
    
    echo $xml;
  }
}
