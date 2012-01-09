<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage eai
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$client_addr = CValue::post("client_addr");
$client_port = CValue::post("client_port");
$port        = CValue::post("port");
$message     = stripslashes(CValue::post("message"));

mbLog($message, "FROM $client_addr:$client_port TO localhost:$port");

$source_mllp = new CSourceMLLP;
$source_mllp->port = $port;
$source_mllp->host = $client_addr;
$source_mllp->loadMatchingObject();

if (!$source_mllp->_id) {
  return;
}

$sender_mllp = CMbObject::loadFromGuid($source_mllp->name);

// Dispatch EAI 
CEAIDispatcher::dispatch($message, $sender_mllp);
