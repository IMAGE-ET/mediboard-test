<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v3AcknowledgmentPRPA
 * Acknowledgment HL7v3
 */
class CHL7v3AcknowledgmentPRPA extends CHL7v3EventPRPA {
  /**
   * Query
   *
   * @param string  $nodeName    The XPath to the node
   * @param DOMNode $contextNode The context node from which the XPath starts
   *
   * @return DOMNodeList
   */
  function query($nodeName, DOMNode $contextNode = null) {
    $xpath = new CHL7v3MessageXPath($contextNode ? $contextNode->ownerDocument : $this);

    if ($contextNode) {
      return $xpath->query($nodeName, $contextNode);
    }

    return $xpath->query($nodeName);
  }

  /**
   * Get the node corresponding to an XPath
   *
   * @param string       $nodeName    The XPath to the node
   * @param DOMNode|null $contextNode The context node from which the XPath starts
   * @param array|null   &$data       Nodes data
   * @param boolean      $root        Is root node ?
   *
   * @return DOMNode The node
   */
  function queryNode($nodeName, DOMNode $contextNode = null, &$data = null, $root = false) {
    $xpath = new CHL7v3MessageXPath($contextNode ? $contextNode->ownerDocument : $this);

    return $data[$nodeName] = $xpath->queryUniqueNode($root ? "//$nodeName" : "$nodeName", $contextNode);
  }

  /**
   * Get the nodeList corresponding to an XPath
   *
   * @param string       $nodeName    The XPath to the node
   * @param DOMNode|null $contextNode The context node from which the XPath starts
   * @param array|null   &$data       Nodes data
   *
   * @return DOMNodeList
   */
  function queryNodes($nodeName, DOMNode $contextNode = null, &$data = null) {
    $nodeList = $this->query("$nodeName", $contextNode);
    foreach ($nodeList as $_node) {
      $data[$nodeName][] = $_node;
    }

    return $nodeList;
  }

  /**
   * Get the text of a node corresponding to an XPath
   *
   * @param string       $nodeName    The XPath to the node
   * @param DOMNode|null $contextNode The context node from which the XPath starts
   *
   * @return string
   */
  function queryTextNode($nodeName, DOMNode $contextNode) {
    $xpath = new CHL7v3MessageXPath($contextNode ? $contextNode->ownerDocument : $this);

    return $xpath->queryTextNode($nodeName, $contextNode);
  }

  /**
   * Get acknowledgment status
   *
   * @return string
   */
  function getStatutAcknowledgment() {
  }
}
