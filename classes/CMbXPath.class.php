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
 * The CMbXPath class
 */
class CMbXPath extends DOMXPath {
  function __construct(DOMDocument $doc) {
    parent::__construct($doc);
  }
  
  function queryUniqueNode($query, DOMNode $contextNode = null, $optional = true) {
    $query = utf8_encode($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);
    
    $erreur = null;
    if ($nodeList->length > 1) {
      throw new Exception("Queried node is not unique, found $nodeList->length occurence(s) for '$query'");
    }

    if (!$optional && $nodeList->length == 0) {
      $erreur  = "Impossible de trouver l'élément '$query'";
      if ($contextNode)
        $erreur .= " dans le contexte : ".$this->nodePath($contextNode);
      throw new Exception($erreur);
    }
    
    return $nodeList->item(0);
  } 
  
  function queryNumcharNode($query, DOMNode $contextNode = null, $length) {
    if (null == $text = $this->queryTextNode($query, $contextNode, " /-.")) {
      return;
    }
    
    $text = substr($text, 0, $length);
    $text = str_pad($text, $length, "0", STR_PAD_LEFT);
    $text = strtr($text, "O", "0"); // Usual trick
    return $text;
  }
  
  function queryTextNode($query, DOMNode $contextNode = null, $purgeChars = "", $addslashes = false) {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = $this->convertEncoding($node->textContent);
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
      if ($addslashes)
        $text = addslashes($text);
    }

    return $text;
  } 

  function queryMultilineTextNode($query, DOMNode $contextNode = null, $prefix = "") {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = $this->convertEncoding($node->textContent);
      if ($prefix) {
        $text = str_replace($prefix, "", $text);
      }
    } 
    
    return $text;
  }
  
  function queryAttributNode($query, DOMNode $contextNode = null, $attName, $purgeChars = "", $optional = true) {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode, $optional)) {
      $text = $this->convertEncoding($node->getAttribute($attName));
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
      $text = addslashes($text);
    }

    return $text;
  }
  
  function getMultipleTextNodes($query, DOMNode $contextNode = null) {
    $array = array();
    $query = $this->convertEncoding($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);
    
    foreach ($nodeList as $n) {
      $array[] = $this->convertEncoding($n->nodeValue);
    }
    return $array;
  }
  
  function getValueAttributNode(DOMNode $node, $attName, $purgeChars = "") {
    $text = "";
    if ($att = $node->getAttributeNode($attName)) {
      $text = $this->convertEncoding($att->value);
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
      $text = addslashes($text);
    }
    
    return $text;
  }
  
  function convertEncoding($value) {
    return utf8_decode($value);
  }
}

?>