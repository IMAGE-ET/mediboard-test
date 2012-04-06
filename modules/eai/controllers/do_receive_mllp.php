<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage eai
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$client_addr = CValue::post("client_addr");
$message     = stripslashes(CValue::post("message"));

$source_mllp = new CSourceMLLP;
$source_mllp->host = $client_addr;
$source_mllp->loadMatchingObject();

if (!$source_mllp->_id) {
  return;
}

$sender_mllp = CMbObject::loadFromGuid($source_mllp->name);

// Dispatch EAI 
$ack = CEAIDispatcher::dispatch($message, $sender_mllp);

ob_clean();

echo $ack;

CApp::rip();
