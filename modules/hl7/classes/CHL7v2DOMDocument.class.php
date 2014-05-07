<?php 

/**
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * HL7v2 DOMDocument
 */
class CHL7v2DOMDocument extends CMbXMLDocument {
  /**
   * Query nodes by XPath
   *
   * @param string           $query       XPath
   * @param CHL7v2DOMElement $contextNode Context node
   *
   * @return CHL7v2DOMElement[]
   */
  function query($query, CHL7v2DOMElement $contextNode = null) {
    $xpath = new CMbXPath($this);

    return $xpath->query($query, $contextNode);
  }

  /**
   * Query text node
   *
   * @param string           $query       Query
   * @param CHL7v2DOMElement $contextNode Context
   *
   * @return string
   */
  function queryTextNode($query, CHL7v2DOMElement $contextNode = null) {
    $xpath = new CMbXPath($this);

    return $xpath->queryTextNode($query, $contextNode);
  }

  /**
   * Query unique node
   *
   * @param string           $query       Query
   * @param CHL7v2DOMElement $contextNode Context
   *
   * @return CHL7v2DOMElement
   */
  function queryUniqueNode($query, CHL7v2DOMElement $contextNode = null) {
    $xpath = new CMbXPath($this);

    return $xpath->queryUniqueNode($query, $contextNode);
  }

  /**
   * Get the element's items
   *
   * @return CHL7v2DOMElement[] The array of items
   */
  function getItems(){
    $items = array();
    foreach ($this->query("//field") as $field) {
      $items[] = $field;
    }
    return $items;
  }
}