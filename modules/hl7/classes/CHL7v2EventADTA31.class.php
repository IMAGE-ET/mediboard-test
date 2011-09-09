<?php

/**
 * A31 - Update person information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Event");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA31");

/**
 * Class CHL7v2EventADTA31 
 * A31 - Add person information
 */
class CHL7v2EventADTA31 extends CHL7v2Event implements CHL7EventADTA31 {
  function __construct(){}
  
  function build($object){}
}

?>