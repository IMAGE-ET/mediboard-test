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
  /**
   * Get the node corresponding to an XPath
   *
   * @param string  $query       The XPath to the node
   * @param DOMNode $contextNode The context node from which the XPath starts
   * @param boolean $optional    Don't throw an exception if node not found
   *
   * @throws Exception
   * @return DOMElement The node
   */
  function queryUniqueNode($query, DOMNode $contextNode = null, $optional = true) {
    $query = utf8_encode($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);

    $erreur = null;
    if ($nodeList->length > 1) {
      throw new Exception("Queried node is not unique, found $nodeList->length occurence(s) for '$query'");
    }

    if (!$optional && $nodeList->length == 0) {
      $erreur  = "Impossible de trouver l'élément '$query'";

      if ($contextNode) {
        $erreur .= " dans le contexte : ".$this->nodePath($contextNode);
      }

      throw new Exception($erreur);
    }

    return $nodeList->item(0);
  }

  /**
   * Get the numchar text of a node corresponding to an XPath
   *
   * @param string   $query       The XPath to the node
   * @param int|null $length      If length is given and is positive, the string returned will contain at most length characters
   * @param DOMNode  $contextNode The context node from which the XPath starts
   *
   * @return string
   */
  function queryNumcharNode($query, $length, DOMNode $contextNode = null) {
    if (null == $text = $this->queryTextNode($query, $contextNode, " /-.")) {
      return;
    }

    $text = substr($text, 0, $length);
    $text = str_pad($text, $length, "0", STR_PAD_LEFT);
    $text = strtr($text, "O", "0"); // Usual trick
    return $text;
  }

  /**
   * Get the text of a node corresponding to an XPath
   *
   * @param string  $query       The XPath to the node
   * @param DOMNode $contextNode The context node from which the XPath starts
   * @param string  $purgeChars  The chars to remove from the text
   * @param boolean $addslashes  Escape slashes is the return string
   *
   * @return string The textual value of the node
   */
  function queryTextNode($query, DOMNode $contextNode = null, $purgeChars = "", $addslashes = false) {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = $this->convertEncoding($node->textContent);

      if ($text == '""') {
        return "";
      }

      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);

      if ($addslashes) {
        $text = addslashes($text);
      }
    }

    return $text;
  }

  /**
   * Get the multiline text of a node corresponding to an XPath
   *
   * @param string  $query       The XPath to the node
   * @param DOMNode $contextNode The context node from which the XPath starts
   * @param string  $prefix      The string to remove from the text
   *
   * @return string The textual value of the node
   */
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

  /**
   * Get the value of attribute
   *
   * @param string  $query       The XPath to the node
   * @param DOMNode $contextNode The context node from which the XPath starts
   * @param string  $attName     Attribute name
   * @param string  $purgeChars  The input string
   * @param bool    $optional    Don't throw an exception if node not found
   *
   * @return string
   */
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

  /**
   * Get multiple text nodes
   *
   * @param string  $query       The XPath to the node
   * @param DOMNode $contextNode The context node from which the XPath starts
   *
   * @return array
   */
  function getMultipleTextNodes($query, DOMNode $contextNode = null) {
    $query = $this->convertEncoding($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);

    $array = array();
    foreach ($nodeList as $n) {
      $array[] = $this->convertEncoding($n->nodeValue);
    }
    return $array;
  }

  /**
   * Get the value of attribute
   *
   * @param DOMNode $node       Node
   * @param string  $attName    Attribute name
   * @param string  $purgeChars The input string
   *
   * @return string
   */
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

  /**
   * Convert value with ISO-8859-1 characters encoded with UTF-8
   *
   * @param string $value Value
   *
   * @return string
   */
  function convertEncoding($value) {
    return utf8_decode($value);
  }
}
