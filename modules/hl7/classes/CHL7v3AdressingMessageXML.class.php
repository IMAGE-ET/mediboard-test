<?php
/**
 * $Id: CHL7v3MessageXML.class.php 21312 2013-12-06 13:35:18Z nicolasld $
 *
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 21312 $
 */

/**
 * Class CHL7v3AdressingMessageXML
 * Message XML adressing
 */
class CHL7v3AdressingMessageXML extends CMbXMLDocument {
  public $patharchiveschema;
  public $hl7v3_version;
  public $dirschemaname;
  public $schemafilename;

  /** @var CExchangeHL7v3 */
  public $_ref_exchange_hl7v3;

  /**
   * Construct
   *
   * @param string $encoding Encoding
   *
   * @return CHL7v3AdressingMessageXML
   */
  function __construct($encoding = "utf-8") {
    parent::__construct($encoding);

    $this->formatOutput = false;
  }
  /**
   * Add namespaces
   *
   * @return void
   */
  function addNameSpaces() {
    $this->addAttribute($this->documentElement, "a:xmlns", "http://www.w3.org/2005/08/addressing");
    $this->addAttribute($this->documentElement, "s:xmlns", "http://www.w3.org/2003/05/soap-envelope");
  }

  /**
   * Add element
   *
   * @param DOMNode $elParent Parent element
   * @param string  $elName   Name
   * @param string  $elValue  Value
   * @param string  $elNS     Namespace
   *
   * @return DOMElement
   */
  function addElement($elParent, $elName, $elValue = null, $elNS = "http://www.w3.org/2005/08/addressing") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }
}
