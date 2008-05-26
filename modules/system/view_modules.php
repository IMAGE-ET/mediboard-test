<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage sytem
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$sql = "SELECT * FROM modules ORDER BY mod_ui_order";
$modules = $this->_spec->ds->loadList($sql);

// get the modules actually installed on the file system
$modFiles = $AppUI->readDirs("modules");

CMbArray::removeValue(".svn", $modFiles);

$coreModules = array();

// do the modules that are installed on the system
foreach ($modules as $keyRow => $row) {
  $modules[$keyRow]["is_setup"]     = @include_once(CAppUI::conf("root_dir")."/modules/".$row["mod_name"]."/setup.php");
  $modules[$keyRow]["is_upToDate"]  = $config["mod_version"] == $row["mod_version"];
  $modules[$keyRow]["is_config"]    = is_file("modules/".$row["mod_name"]."/configure.php");
  $modules[$keyRow]["href"]         = "?m=$m&a=domodsql&mod_id=".$row["mod_id"];
  
  if($config["mod_type"] == "core") {
    if(!$modules[$keyRow]["is_upToDate"]) {
      $coreModules[] = $modules[$keyRow];
    }
  }

  if(isset($modFiles[$row["mod_name"]])) {
    unset($modFiles[$row["mod_name"]]);
  } 
}

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;
$smarty->assign("modules"     , $modules);
$smarty->assign("coreModules" , $coreModules);
$smarty->assign("modFiles"    , $modFiles);

$smarty->display("view_modules.tpl");

?>