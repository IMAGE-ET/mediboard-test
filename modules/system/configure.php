<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

// Chargement des fuseaux horaires
$zones = timezone_identifiers_list();
$continents = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
$timezones = array();
foreach ($zones as $zone) {
  $parts = explode('/', $zone, 2); // 0 => Continent, 1 => City
  
  // Only use "friendly" continent names
  if (isset($parts[1]) && in_array($parts[0], $continents)) {
    $timezones[$parts[0]][$zone] = str_replace('_', ' ', $parts[1]); // Creates array(DateTimeZone => 'Friendly name')
  }
}

$php_config = ini_get_all();
$php_config_important = array(
  "memory_limit",
  "default_socket_timeout",
  "max_execution_time",
  "mysql.connect_timeout",
  "session.gc_maxlifetime",
);
$php_config_tree = array(
  "general" => array()
);
foreach($php_config as $key => $value) {
  $parts = explode(".", $key, 2);
  $value["user"] = $value["access"] & 1;
  if (count($parts) == 1) {
    $php_config_tree["general"][$key] = $value;
  }
  else {
    if (!isset($php_config_tree[$parts[0]])) 
      $php_config_tree[$parts[0]] = array();
    $php_config_tree[$parts[0]][$key] = $value;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("timezones", $timezones);
$smarty->assign("php_config", $php_config_tree);
$smarty->assign("php_config_important", $php_config_important);
$smarty->display("configure.tpl");

?>