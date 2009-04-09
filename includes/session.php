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
?>