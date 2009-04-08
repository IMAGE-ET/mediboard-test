<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CMbXPath class
 */
class CMbXPath extends DOMXPath {
  function __construct(DOMDocument $doc) {
    parent::__construct($doc);
  }

  function queryUniqueNode($query, DOMNode $contextNode = null) {
    $query = utf8_encode($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);
    if ($nodeList->length > 1) {
      trigger_error("queried node is not unique, found $nodeList->length occurence(s) for '$query'", E_USER_WARNING);
      return null;
    }
    return $nodeList->item(0);
  } 
  
  function queryNumcharNode($query, DOMNode $contextNode, $length) {
    if (null == $text = $this->queryTextNode($query, $contextNode, " /-.")) {
      return;
    }
    
    $text = substr($text, 0, $length);
    $text = str_pad($text, $length, "0", STR_PAD_LEFT);
    $text = strtr($text, "O", "0"); // Usual trick
    return $text;
  }
  
  function queryTextNode($query, DOMNode $contextNode, $purgeChars = "", $addslashes = true) {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = utf8_decode($node->textContent);
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
      if ($addslashes)
        $text = addslashes($text);
    }

    return $text;
  } 

  function queryMultilineTextNode($query, DOMNode $contextNode, $prefix = "") {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = utf8_decode($node->textContent);
      if ($prefix) {
        $text = str_replace($prefix, "", $text);
      }
    } 
    
    return $text;
  }
  
  function queryAttributNode($query, DOMNode $contextNode, $attName, $purgeChars = "") {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = utf8_decode($node->getAttribute($attName));
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
      $text = addslashes($text);
    }

    return $text;
  }
  
  function getMultipleTextNodes($query, DOMNode $contextNode) {
    $array = array();
    $query = utf8_encode($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);
    
    foreach ($nodeList as $n) {
      $array[] = utf8_decode($n->nodeValue);
    }
    return $array;
  }
}

?>