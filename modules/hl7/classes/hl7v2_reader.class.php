<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Reader {  
  
  /**
   * Read HL7 file
   * @param $fileName Filename
   */
  function readFile($fileName) {    
    $message = new CHL7v2Message();
    try {
      $fileContents = file_get_contents($fileName);
      $fileContents = str_replace("\r\n", "\n", $fileContents);
      $fileContents = str_replace("\n", "\n", $fileContents);
      $message->parseMessage($fileContents);
    } catch (Exception $e) {
      exceptionHandler($e);
      return;
    }
  }
}

?>