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
class CCDADomDocument extends DOMDocument {

  /**
   * Création du xml en UTF-8
   *
   * @param String $encoding String
   */
  function __construct($encoding = "UTF-8") {
    parent::__construct("1.0", $encoding);

    $this->preserveWhiteSpace = false;
    $this->formatOutput = true;
  }

  /**
   * Permet de créer le noeud root
   *
   * @param String $name      String
   * @param String $namespace String
   *
   * @return void
   */
  function createNodeRoot($name, $namespace = null) {
    $name = utf8_encode($name);
    if (empty($namespace)) {
      $this->appendChild($this->createElement($name));
    }
    else {
      $this->appendChild($this->createElementNS($namespace, $name));
    }

  }

  /**
   * Permet de créer le noeud root avec le namespace urn:hl7-org:v3
   *
   * @param String $namespace String
   * @param String $name      String
   *
   * @return void
   */
  function createNodeRootNS($namespace, $name) {
    $name = utf8_encode($name);
    $this->appendChild($this->createElementNS($namespace, $name));
  }

  /**
   * Retourne le premier élément trouvé
   *
   * @param String $name String
   *
   * @return DOMElement
   */
  function getElement($name) {
    $name = utf8_encode($name);
    return $this->getElementsByTagName($name)->item(0);
  }

  /**
   * Ajoute un attribut avec sa valeur au noeud spécifié
   *
   * @param DOMElement $nodeParent DOMElement
   * @param String     $name       String
   * @param String     $value      String
   *
   * @return void
   */
  function appendAttribute($nodeParent, $name, $value) {
    $name = utf8_encode($name);
    $value = utf8_encode($value);
    $nodeParent->appendChild($this->createAttribute($name));
    $nodeParent->attributes->getNamedItem($name)->nodeValue = $value;
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
   * Importe un DOMDocument à l'intérieur de l'élément spécifié
   *
   * @param DOMElement  $nodeParent  DOMElement
   * @param DOMDocument $domDocument DOMDocument
   *
   * @return void
   */
  function importDOMDocument($nodeParent, $domDocument) {
    $nodeParent->appendChild($this->importNode($domDocument->documentElement, true));
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
   * Enlève les éléments vide du document
   *
   * @return void
   */
  function purgeEmptyElements() {
    $this->purgeEmptyElementsNode($this->documentElement);
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
