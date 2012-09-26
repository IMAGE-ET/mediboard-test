<?php /** $Id$ **/
/**
 * @package    Mediboard
 * @subpackage dicom
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * A Dicom server listening with a socket of a port
 */
class CDicomServer extends CSocketBasedServer {
  
  /**
   * The module 
   * 
   * @var string
   */
  protected $module = "dicom";
  
  /**
   * The controller who will receive the messages
   * 
   * @var string
   */
  protected $controller = "do_receive_message";
  
  /**
   * Check if the message is complete
   * 
   * @param string $message The message
   * 
   * @return boolean
   */
  function isMessageFull($message) {
    $length = unpack("N", substr($message, 2, 4));
    if ($length == strlen($message) - 6) {
      return true;
    }
    return false;
  }
  
  /**
   * A sample Dicom message
   *  
   * @return string
   */
  final static function sampleMessage() {
    return "";
  }
}
?> 