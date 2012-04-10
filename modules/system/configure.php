<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

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

$browser_compat = array(
  'firefox' => array('2.0', '3.0', '3.5', '3.6', '4.0', '5.0', '6.0', '7.0', '8.0', '9.0', '10.0', '11.0', '12.0', '13.0', '14.0'),
  'msie'    => array('7.0', '8.0', '9.0', '10.0'),
  'opera'   => array('9.0', '9.6', '10.0', '10.1', '10.7', '11.0', '12.0'),
  'chrome'  => array('5.0', '6.0', '7.0', '8.0', '9.0', '10.0', '11.0', '12.0', '13.0', '14.0', '15.0', '16.0', '17.0', '18.0', '19.0', '20.0'),
  'safari'  => array(
    '525.26' => '3.2',
    '525.27' => '3.2.1',
    '525.28' => '3.2.3',
    '530.17' => '4.0',
    '533.16' => '4.1 / 5.0',
    '533.17.8' => '4.1.1 / 5.0.1',
    '533.19.4' => '5.0.3',
    '534.48.3' => '5.1',
    '534.55.3' => '5.1.5',
  ),
);

// Source SMTP
$message_smtp = CExchangeSource::get("system-message", "smtp", true);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("timezones"           , $timezones);
$smarty->assign("php_config"          , $php_config_tree);
$smarty->assign("php_config_important", $php_config_important);
$smarty->assign("browser_compat"      , $browser_compat);
$smarty->assign("message_smtp"        , $message_smtp);
CModelObject::makeHandlers();
$smarty->assign("handlers"            , CModelObject::getHandlers());
$smarty->display("configure.tpl");

?>