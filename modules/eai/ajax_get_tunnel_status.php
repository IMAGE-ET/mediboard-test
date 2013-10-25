<?php 

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Déverrouiller la session pour rendre possible les requêtes concurrentes.
CSessionHandler::writeClose();

$source_guid = CValue::get("source_guid");

$status = null;

/** @var CHTTPTunnelObject $tunnel */
$tunnel = CMbObject::loadFromGuid($source_guid);

$reachable = $tunnel->checkStatus();

$status = array(
  "reachable" => $reachable,
  "message"   => $tunnel->_message_status
);

echo json_encode($status);