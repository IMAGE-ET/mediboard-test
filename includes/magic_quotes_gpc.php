<?php
/**
 * Input arrays preperations
 * 
 * @package    Mediboard
 * @subpackage includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Id$
 */

// Emulates magic quotes when disabled
if (!get_magic_quotes_gpc()) {
  $_GET     = array_map_recursive("addslashes", $_GET);
  $_POST    = array_map_recursive("addslashes", $_POST);
  $_COOKIE  = array_map_recursive("addslashes", $_COOKIE);
  $_REQUEST = array_map_recursive("addslashes", $_REQUEST);
}

// UTF decode inputs from ajax requests
if (isset($_REQUEST["ajax"]) && empty($_REQUEST["accept_utf8"])) {
  $_GET     = array_map_recursive("utf8_decode", $_GET);
  $_POST    = array_map_recursive("utf8_decode", $_POST);
  $_COOKIE  = array_map_recursive("utf8_decode", $_COOKIE);
  $_REQUEST = array_map_recursive("utf8_decode", $_REQUEST);
}
