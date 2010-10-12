<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2XPath extends CHL7v2Exception {
  static function queryUniqueNode(SimpleXMLElement $xmlElement, $query) {
    $query = utf8_encode($query);
    
    $nodeList = $xmlElement->xpath($query);
    if (count($nodeList) > 1) {
      throw new CHL7v2Exception("Queried node is not unique, found ".count($nodeList)." occurence(s) for '$query'", CHL7v2Exception::INVALID_UNIQUE_NODE);
    }
    
    return $nodeList[0];
  }
  
  static function queryTextNode(SimpleXMLElement $xmlElement, $query, $purgeChars = "") {
    $text = "";
    if ($node = self::queryUniqueNode($xmlElement, $query)) {
      $node = reset($node);
      $text = utf8_decode($node);
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
    }

    return $text;
  }
  
  static function queryCountNode(SimpleXMLElement $xmlElement, $query) {
    return count($xmlElement->xpath($query));
  }
  
  static function queryMultipleNodes(SimpleXMLElement $xmlElement, $query) {
    return reset(self::queryUniqueNode($xmlElement, $query));
  }
  
  static function queryAttributNode(SimpleXMLElement $xmlElement, $query, $attName, $purgeChars = "") {
    $text = "";
    if ($node = self::queryUniqueNode($xmlElement, $query)) { 
      if (!$attributes = $node->attributes()) {
        return $text;
      }
      $attributes = reset($node->attributes());
      if (array_key_exists($attName, $attributes)) {
        $text = utf8_decode($attributes[$attName]);
        $text = str_replace(str_split($purgeChars), "", $text);
        $text = trim($text);
        $text = addslashes($text);
      }
    }

    return $text;
  }
}

?>