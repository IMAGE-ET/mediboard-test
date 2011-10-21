<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$version = CValue::getOrSession("version", "2.5");

$version_dir = "hl7v".preg_replace("/[^0-9]/", "_", $version);
$schema_path = CHL7v2::LIB_HL7."/$version_dir/";

$schemas = array(
  "message" => null,
  "segment" => null,
  "composite" => null,
);

foreach($schemas as $type => $composite) {
  $paths = glob($schema_path."$type*.xml");
  
  foreach($paths as $path) {
    preg_match("/$type(.+)\.xml$/", $path, $matches);
    $name = $matches[1];
    
    if ($type == "message" && strlen($name) > 3) {
      $prefix = substr($name, 0, 3);
      if (!isset($schemas[$type][$prefix])) {
        $schemas[$type][$prefix] = array();
      }
      $schemas[$type][$prefix][substr($name, 3)] = $path;
    }
    else {
      $schemas[$type][$name] = $path;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("schemas", $schemas);
$smarty->assign("version", $version);
$smarty->display("vw_hl7v2_schemas.tpl");

?>