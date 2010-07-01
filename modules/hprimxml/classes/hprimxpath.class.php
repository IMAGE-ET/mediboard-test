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
  
  function getMultipleTextNodes($query, DOMNode $contextNode, $implode = false) {
    $array = array();
    $query = utf8_encode($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);
    
    foreach ($nodeList as $n) {
      $array[] = utf8_decode($n->nodeValue);
    }
    return $implode ? implode(" ", $array) : $array;
  }
  
  function getFirstNode($query, DOMNode $contextNode) {
    $textNodes = $this->getMultipleTextNodes($query, $contextNode);
    //mbTrace($textNodes, "nodes", true);
    
    return isset($textNodes[0]) ? $textNodes[0] : null;
  }
  
  
}

?>