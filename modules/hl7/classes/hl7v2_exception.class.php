<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Exception extends Exception {  
  const EMPTY_MESSAGE              = 1;
  const INVALID_ENTERED_HEADER     = 2;
  const INVALID_SEPARATOR          = 3;
  const SEGMENT_NAME_TOO_SHORT     = 4;
  const INVALID_SEGMENT_CHARACTERS = 5;
  const TOO_FEW_SEGMENT_FIELDS     = 6;
  const TOO_MANY_FIELDS            = 7;
  const SPECS_FILE_MISSING         = 8;
  const INVALID_UNIQUE_NODE        = 9;
  
  public function __construct($text = null, $code = 0) { 
    parent::__construct(CAppUI::tr("CHL7v2Exception-$code")." : '$text'", $code); 
  } 
}



?>