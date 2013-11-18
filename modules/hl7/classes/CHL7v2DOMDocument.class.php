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
   * @return self[]
   */
  function query($query, CHL7v2DOMElement $contextNode = null) {
    $xpath = new CMbXPath($this);
    return $xpath->query($query, $contextNode);
  }

  function queryTextNode($query, CHL7v2DOMElement $contextNode = null) {
    $xpath = new CMbXPath($this);
    return $xpath->queryTextNode($query, $contextNode);
  }

  /**
   * @param                  $query
   * @param CHL7v2DOMElement $contextNode
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
   * @return array The array of items
   */
  function getItems(){
    $items = array();
    foreach ($this->query("//field") as $field) {
      $items[] = $field;
    }
    return $items;
  }
}