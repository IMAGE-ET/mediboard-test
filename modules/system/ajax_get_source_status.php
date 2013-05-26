<?php
/**
 * Status exchange source
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Déverrouiller la session pour rendre possible les requêtes concurrentes.
session_write_close();

$source_guid = CValue::get("source_guid");

$status = null;

/** @var CExchangeSource $source */
$source = CMbObject::loadFromGuid($source_guid);

$source->isReachable();
$source->getResponseTime();

$status = array(
  "reachable"     => $source->_reachable,
  "message"       => utf8_encode($source->_message),
  "name"          => $source->name,
  "response_time" => $source->_response_time,
);

echo json_encode($status);
