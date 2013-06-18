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
  public $patharchiveschema;
  public $hl7v3_version;
  public $dirschemaname;
  public $schemafilename;

  /** @var CExchangeHL7v3 */
  public $_ref_exchange_hl7v3;

  /** @var CInteropSender */
  public $_ref_sender;

  /** @var CInteropReceiver */
  public $_ref_receiver;

  /**
   * Construct
   *
   * @param string $encoding      Encoding
   * @param string $hl7v3_version Version
   *
   * @return \CHL7v3MessageXML
   */
  function __construct($encoding = "utf-8", $hl7v3_version = null) {
    parent::__construct($encoding);

    $this->formatOutput  = true;
    $this->hl7v3_version = $hl7v3_version;
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
   * Try to validate the document against a schema will trigger errors when not validating
   *
   * @param bool $returnErrors   Return errors
   * @param bool $display_errors Display errors
   *
   * @return boolean
   */
  function schemaValidate($returnErrors = false, $display_errors = true) {
    $this->patharchiveschema = "modules/hl7/resources/hl7v3_$this->hl7v3_version";
    $this->schemafilename    = "$this->patharchiveschema/$this->dirschemaname.xsd";

    return parent::schemaValidate($this->schemafilename, $returnErrors, $display_errors);
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
