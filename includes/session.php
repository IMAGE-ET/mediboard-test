<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
// Load AppUI from session
$rootName = basename(CAppUI::conf("root_dir"));

// Manage the session variable(s)
session_name("$rootName-session");
if (get_cfg_var("session.auto_start") > 0) {
  session_write_close();
}
session_start();
session_register("AppUI");
  
// Check if session has previously been initialised
if(!isset($_SESSION["AppUI"]) || isset($_GET["logout"])) {
  $_SESSION["AppUI"] = new CAppUI();
}

$AppUI =& $_SESSION["AppUI"]; 

/** Basic browser detection */ 
$browser = array(
  'version' => '0.0.0',
  'majorver' => 0,
  'minorver' => 0,
  'build' => 0,
  'name' => 'unknown'
);

$browsers = array(
  'firefox', 'msie', 'opera', 'chrome', 'safari', 'mozilla', 'seamonkey', 'konqueror', 'netscape',
  'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 'omniweb', 'avant', 'camino', 'flock', 'aol'
);

$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
foreach($browsers as $_browser) {
  if (preg_match("/($_browser)[\/ ]?([0-9.]*)/", $user_agent, $match)) {
    $browser['name'] = $match[1];
    $browser['version'] = $match[2];
    preg_match( "/^(\d+)\.(\d+)(?:\.(\d+))?$/", $browser['version'], $match);
    $browser['majorver'] = $match[1];
    $browser['minorver'] = $match[2];
    $browser['build'] = isset($match[3]) ? $match[3] : 0;
    break;
  }
}
