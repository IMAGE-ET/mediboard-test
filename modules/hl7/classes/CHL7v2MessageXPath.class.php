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
 * Class CHL7v2MessageXML 
 * XPath HL7
 */
class CHL7v2MessageXPath extends CMbXPath {
  function __construct(DOMDocument $dom) {
    parent::__construct($dom);
    
    $this->registerNamespace("hl7", "urn:hl7-org:v2xml");
  }
  
  function convertEncoding($value) {
    return $value;
  }
}
