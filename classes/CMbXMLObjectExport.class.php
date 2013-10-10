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

class CMbXMLObjectExport extends CMbXMLDocument {
  public $object_class;
  public $object_id;
  
  public $objects_values = array();
  
  function load($file, $options = null){
    parent::load($file);
    
    $root = $this->documentElement;
    
    list($this->object_class, $this->object_id) = explode("-", $root->nodeName);
    
    $objectNodes = $root->childNodes;
    $objects = array();
    
    foreach ($objectNodes as $node) {
      $values = $this->getFields($node);
      $refs = $this->getRefs($node);
      $objects[$node->getAttribute("id")] = new CMbObjectImport($node->nodeName, $values, $refs);
    }
    
    $this->objects_values = $objects;
  }
  
  function getFields(DOMNode $node) {
    $fields = array();
    
    foreach ($node->childNodes as $_node) {
      $fields[$_node->nodeName] = $_node->nodeValue;
    }
    
    return $fields;
  }
  
  function getRefs(DOMNode $node) {
    $refs = array();
    $attributes = $node->attributes;
    
    for ($i = 0; $i < $attributes->length; $i++) {
      $_attr = $attributes->item($i);
      $refs[$_attr->nodeName] = $_attr->nodeValue;
    }
    
    return $refs;
  }
}
