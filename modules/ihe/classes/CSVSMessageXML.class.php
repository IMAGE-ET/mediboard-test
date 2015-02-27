<?php
/**
 * $Id: CSVSMessageXML.class.php 27061 2015-02-05 08:02:38Z lryo $
 *
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 27061 $
 */

/**
 * Class CSVSMessageXML
 * Message XML SVS
 */
class CSVSMessageXML extends CHL7v3MessageXML {
  /**
   * Construct
   *
   * @param string $encoding Encoding
   * @param string $version  Version
   *
   * @return \CSVSMessageXML
   */
  function __construct($encoding = "utf-8", $version = "2008") {
    parent::__construct($encoding, $version);

    $this->formatOutput  = true;
    $this->dirschemaname = "SVS";
  }

  /**
   * Add namespaces
   *
   * @return void
   */
  function addNameSpaces() {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns", "urn:ihe:iti:svs:2008");
  }

  /**
   * Add element
   *
   * @param DOMNode $elParent Parent element
   * @param string  $elName   Name
   * @param string  $elValue  Value
   * @param string  $elNS     Namespace
   *
   * @return mixed
   */
  function addElement($elParent, $elName, $elValue = null, $elNS = "urn:ihe:iti:svs:2008") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }

  /**
   * Add value set
   *
   * @param DOMNode $elParent Parent element
   * @param string  $attName  Attribute name
   * @param string  $attValue Attribute value
   * @param array   $data     Data
   *
   * @return void
   */
  function addValueSet($elParent, $attName, $attValue, $data = array()) {
    if (!$value = CMbArray::get($data, $attValue)) {
      return;
    }

    $this->addAttribute($elParent, $attName, $value);
  }

  /**
   * Création d'élément WS-Addressing
   *
   * @param DOMNode $nodeParent Noeud parent
   * @param String  $name       Nom du noeud
   * @param String  $value      Valeur du noeud
   *
   * @return DOMElement
   */
  function createAddressingElement($nodeParent, $name, $value = null) {
    return $this->addElement($nodeParent, $name, $value, "http://www.w3.org/2005/08/addressing");
  }
}
