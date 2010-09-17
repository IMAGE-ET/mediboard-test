<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2MessageException extends Exception {  
  const EMPTY_MESSAGE     = 1;
  const INVALID_SEPARATOR = 2;
  
  public function __construct($message = null, $code = 0) { 
    parent::__construct(CAppUI::tr("CHL7v2MessageException-$code")." : '$message'", $code); 
  } 
}



?>