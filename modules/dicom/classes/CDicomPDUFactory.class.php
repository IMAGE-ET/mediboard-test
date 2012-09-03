<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem | llemoine
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * The PDU Factory, who matches the type of PDU and the corresponding PHP class
 */
class CDicomPDUFactory {

  /**
   * Make the link between the code types and the PDU classes
   * 
   * @var array
   */
  static $pdu_types = array(
    "01" => "CDicomPDUAAssociateRQ",
    "02" => "CDicomPDUAAssociateAC",
    "03" => "CDicomPDUAAssociateRJ",
    "04" => "CDicomPDUPDataTF",
    "05" => "CDicomPDUAReleaseRQ",
    "06" => "CDicomPDUAReleaseRP",
    "07" => "CDicomPDUAAbort",
  );
  
  /**
   * Get the type of the PDU, and create the corresponding CDicomPDU
   * 
   * @param resource $stream A stream, file or socket
   * 
   * @return CDicomPDU The PDU
   */
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
  
  /**
   * Create a PDU of the given type
   * 
   * @param resource $stream A stream, file or socket
   * 
   * @param string   $type   The type of the PDU you want to create
   * 
   * @param array    $datas  The differents datas of the PDU
   * 
   * @return CDicomPDU The PDU
   */
  static function encodePDU($stream , $type, $datas) {
    if (!$stream) {
      // retourne une erreur
    }
    $stream_writer = new CDicomStreamWriter($stream);

    $pdu_type = self::$pdu_types[$type];
    
    $pdu = new $pdu_type($datas);
    $pdu->encodePDU($stream_writer);
    
    return $pdu;
  }
}
?>