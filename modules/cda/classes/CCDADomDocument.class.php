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
   * Permet de créer le noeud root
   *
   * @param String $name String
   *
   * @return void
   */
  function createNodeRoot($name) {
    $name = utf8_encode($name);
    $this->appendChild($this->createElement($name));
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
}
