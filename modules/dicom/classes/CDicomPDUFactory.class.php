<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem | llemoine
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * Description
 */
class CDicomPDUFactory {

  static $pdu_types = array(
    "01" => "CDicomPDUAAssociateRQ",
    "02" => "CDicomPDUAAssociateAC",
    "03" => "CDicomPDUAAssociateRJ",
    "04" => "CDicomPDUPDataTF",
    "05" => "CDicomPDUAReleaseRQ",
    "06" => "CDicomPDUAReleaseRP",
    "07" => "CDicomPDUAAbort",
  );
  
  static function decodePDU($stream) {
    if (!$stream) {
      // retourne une erreur
    }
    $stream_reader = new CDicomStreamReader($stream);

    $pdu_type = self::$pdu_types[$stream_reader->readHexByte()];
    
    $pdu = new $pdu_type();
    $pdu->decodePDU($stream_reader);
    
    return $pdu;
  }
  
  static function encodePDU($type) {
    
  }
}
?>