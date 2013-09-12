<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Permet de cré
 */
class CCDADomDocument extends CMbXMLDocument {

  /**
   * Création du xml en UTF-8
   */
  function __construct() {
    parent::__construct("UTF-8");

    $this->preserveWhiteSpace = true;
    $this->formatOutput = false;
  }

  /**
   * Ajoute du text en premier position
   *
   * @param DOMElement $nodeParent DOMElement
   * @param String     $value      String
   *
   * @return void
   */
  function insertTextFirst($nodeParent, $value) {
    $value = utf8_encode($value);
    $firstNode = $nodeParent->firstChild;
    $nodeParent->insertBefore($this->createTextNode($value), $firstNode);
  }

  /**
   * Caste l'élement spécifié
   *
   * @param DOMNode $nodeParent DOMNode
   * @param String  $value      String
   *
   * @return void
   */
  function castElement($nodeParent, $value) {
    $value = utf8_encode($value);
    $attribute = $this->createAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:type");
    $attribute->nodeValue = $value;
    $nodeParent->appendChild($attribute);
  }

  /**
   * Enlève les élements vide d'un noeud
   *
   * @param DOMElement $node DOMElement
   *
   * @return void
   */
  function purgeEmptyElementsNode($node) {
    // childNodes undefined for non-element nodes (eg text nodes)
    if ($node->childNodes) {
      // Copy childNodes array
      $childNodes = array();
      foreach ($node->childNodes as $childNode) {
        $childNodes[] = $childNode;
      }

      // Browse with the copy (recursive call)
      foreach ($childNodes as $childNode) {
        $this->purgeEmptyElementsNode($childNode);
      }
    }
    // Remove if empty
    if (!$node->hasChildNodes() && !$node->hasAttributes() && $node->nodeValue === "") {
      $node->parentNode->removeChild($node);
    }
  }
}