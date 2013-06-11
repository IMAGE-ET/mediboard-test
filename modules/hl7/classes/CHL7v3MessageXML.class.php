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
 * Class CHL7v3MessageXML
 * Message XML HL7
 */
class CHL7v3MessageXML extends CMbXMLDocument {
  /** @var CExchangeHL7v3 */
  public $_ref_exchange_hl7v3;

  /** @var CInteropSender */
  public $_ref_sender;

  /** @var CInteropReceiver */
  public $_ref_receiver;

  /**
   * Construct
   *
   * @param string $encoding Encoding
   *
   * @return \CHL7v3MessageXML
   */
  function __construct($encoding = "utf-8") {
    parent::__construct($encoding);

    $this->formatOutput = true;
  }

  /**
   * Transforms absolute datetime into HL7v3 DATETIME format
   *
   * @return string|datetime The datetime
   **/
  static function dateTime() {
    return CMbDT::format(CMbDT::dateTime(), "%Y%m%d%H%M%S");
  }

  /**
   * Add namespaces
   *
   * @return void
   */
  function addNameSpaces() {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns", "urn:hl7-org:v3");

    $this->addAttribute($this->documentElement, "ITSVersion", "XML_1.0");
  }

  /**
   * Add element
   *
   * @param string $elParent Parent element
   * @param string $elName   Name
   * @param string $elValue  Value
   * @param string $elNS     Namespace
   *
   * @return DOMElement
   */
  function addElement($elParent, $elName, $elValue = null, $elNS = "urn:hl7-org:v3") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }

  /**
   * Handle event
   *
   * @param CHL7Acknowledgment $ack    Acknowledgment
   * @param CMbObject          $object Object
   * @param array              $data   Data
   *
   * @return void|string
   */
  function handle($ack, CMbObject $object, $data) {
  }
}
