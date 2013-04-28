<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Files
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CDocumentSender abstract class
 */
abstract class CDocumentSender {
  function send(CDocumentItem $docItem) {}
    
  function cancel(CDocumentItem $docItem) {}
  
  function resend(CDocumentItem $docItem) {}
  
  function getSendProblem(CDocumentItem $docItem) {}
}
