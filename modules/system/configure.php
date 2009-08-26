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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("now", mbDateTime());
$smarty->assign("timezones", $timezones);

$smarty->display("configure.tpl");

?>