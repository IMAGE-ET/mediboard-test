<?php
/**
 *  @package Mediboard
 *  @subpackage dPfiles
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CDocumentSender abstract class
 */
abstract class CDocumentSender {
  function send(CDocumentItem $docItem) {}
    
  function cancel(CDocumentItem $docItem) {}
  
  function resend(CDocumentItem $docItem) {}
  
  function isSendable(CDocumentItem $docItem) {}
}
?>