<?php /* $Id: hprimxmldocument.class.php 9055 2010-05-28 11:56:08Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 9055 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXPath extends CMbXPath {  
  function __construct(DOMDocument $doc) {
    parent::__construct($doc);
    
    $this->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
  }
  
  function queryTextNode($query, DOMNode $contextNode, $purgeChars = "", $addslashes = false) {
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
  
  function queryMultilineTextNode($query, DOMNode $contextNode, $prefix = "", $implode = false) {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = utf8_decode($node->textContent);
      if ($prefix) {
        $text = str_replace($prefix, "", $text);
      }
    } 
    
    return $text;
  }
  
  function getFirstNode($query, DOMNode $contextNode) {
    $textNodes = getMultipleTextNodes($query, $contextNode);
    
    return isset($textNodes[0]) ? $textNodes[0] : null;
  }
  
  
}

?>