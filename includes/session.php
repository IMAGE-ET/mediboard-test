<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
// Load AppUI from session
global $rootName;

// Manage the session variable(s)
session_name(preg_replace("/[^a-z0-9]/i", "", $rootName));

if (get_cfg_var("session.auto_start") > 0) {
  session_write_close();
}

session_start();
  
// Check if session has previously been initialised
if(empty($_SESSION["AppUI"]) || isset($_GET["logout"])) {
  $_SESSION["AppUI"] = CAppUI::init();
}

CAppUI::$instance =& $_SESSION["AppUI"];
$AppUI =& CAppUI::$instance; //@todo: $AppUI shouldn't be used anymore

if (!isset($_SESSION["locked"])) $_SESSION["locked"] = false; 

if (!isset($_SESSION['browser'])) {
  /** Basic browser detection */ 
  $browser = array(
    'version'   => '0.0.0',
    'majorver'  => 0,
    'minorver'  => 0,
    'build'     => 0,
    'name'      => 'unknown',
    'useragent' => ''
  );
  
  $browsers = array(
    'firefox', 'msie', 'opera', 'chrome', 'safari', 'mozilla', 'seamonkey', 'konqueror', 'netscape',
    'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 'omniweb', 'avant', 'camino', 'flock', 'aol'
  );
  
  $minimal_versions = array(
    'firefox' => '3.0',
    'msie'    => '7.0',
    'opera'   => '9.6',
    'chrome'  => '4.0',
    'safari'  => '525.26', // 3.2
  );
  
  if (isset($minimal_versions[$browser['name']]) && 
      $browser['version'] < $minimal_versions[$browser['name']]) {
    mbTrace($browser['useragent'], "old browser", true);
  }
  
  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $browser['useragent'] = $_SERVER['HTTP_USER_AGENT'];
    $user_agent = strtolower($browser['useragent']);
    foreach($browsers as $_browser) {
      if (preg_match("/($_browser)[\/ ]?([0-9.]*)/", $user_agent, $match)) {
        $browser['name'] = $match[1];
        $browser['version'] = $match[2];
        @list($browser['majorver'], $browser['minorver'], $browser['build']) = explode('.', $browser['version']);
        break;
      }
    }
  }
  
  $_SESSION['browser'] =& $browser; 
}
else $browser =& $_SESSION['browser']; 
