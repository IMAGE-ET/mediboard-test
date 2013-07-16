<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link       http://www.mediboard.org
 */

$human_long_request_level = CAppUI::conf("human_long_request_level");
$bot_long_request_level   = CAppUI::conf("bot_long_request_level");

if (!$human_long_request_level && !$bot_long_request_level) {
  return;
}

$duration = CApp::$performance["genere"];

if (!CAppUI::$user) {
  return;
}
$bot = CAppUI::$user->isRobot();

// Determine the log_level to apply
$long_request_log_level = false;
if ($bot && $bot_long_request_level) {
  $long_request_log_level = $bot_long_request_level;
}
elseif (!$bot && $human_long_request_level) {
  $long_request_log_level = $human_long_request_level;
}

if (!$long_request_log_level) {
  return;
}

// If request is too slow
if ($duration > $long_request_log_level) {
  // We store it
  $long_request_log = new CLongRequestLog();
  $long_request_log->datetime    = CMbDT::format(null, "%Y-%m-%d %H:%M:00");
  $long_request_log->duration    = $duration;
  $long_request_log->server_addr = get_server_var('SERVER_ADDR');
  $long_request_log->user_id     = CAppUI::$user->_id;

  // GET and POST params
  $long_request_log->_query_params_get  = $_GET;
  $long_request_log->_query_params_post = $_POST;

  $session = $_SESSION;
  unset($session['AppUI']);
  unset($session['dPcompteRendu']['templateManager']);

  // SESSION params
  $long_request_log->_session_data = $session;

  // Unique Request ID
  $long_request_log->requestUID = CApp::getRequestUID();

  if ($msg = $long_request_log->store()) {
    trigger_error($msg, E_USER_WARNING);
  }
}