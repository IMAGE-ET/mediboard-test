<?php /* $Id:$ */

/**
 * XPath HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2MessageXML 
 * XPath HL7
 */

class CHL7v2MessageXPath extends CMbXPath {
  function __construct(DOMDocument $dom) {
    parent::__construct($dom);
    
    $this->registerNamespace( "hl7", "urn:hl7-org:v2xml" );
  }
}

?>