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

class CHL7v2Reader {  
  
  /**
   * Read HL7 file
   * @param $fileName Filename
   */
  function readFile($fileName) {
    $message = new CHL7v2Message();
    
    try {
      $fileContents = file_get_contents($fileName);
      $message->parse($fileContents);
    } catch (Exception $e) {
      exceptionHandler($e);
      return;
    }
  
    return $message;
  }
}
