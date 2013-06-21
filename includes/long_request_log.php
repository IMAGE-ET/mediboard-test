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

$long_request_log_level = CAppUI::conf("long_request_log_level");

if (!$long_request_log_level) {
  return;
}

$duration = CApp::$performance["genere"];

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