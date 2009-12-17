<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Emulates magic quotes when disabled
if (!get_magic_quotes_gpc()) {
  $_GET     = array_map_recursive("addslashes", $_GET);
  $_POST    = array_map_recursive("addslashes", $_POST);
  $_COOKIE  = array_map_recursive("addslashes", $_COOKIE);
  $_REQUEST = array_map_recursive("addslashes", $_REQUEST);
}

if(isset($_REQUEST["ajax"])){
  $_GET     = array_map_recursive("utf8_decode", $_GET);
  $_POST    = array_map_recursive("utf8_decode", $_POST);
  $_COOKIE  = array_map_recursive("utf8_decode", $_COOKIE);
  $_REQUEST = array_map_recursive("utf8_decode", $_REQUEST);
}
