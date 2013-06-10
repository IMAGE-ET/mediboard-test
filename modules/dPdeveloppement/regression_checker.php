<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien Ménager
*/

CCanDo::checkRead();

$root_dir = CAppUI::conf("root_dir");
$extensions = array("php");
$ignored = array(
  "classes/",
  "locales/",
  "controllers/",
  "shell/",
  "/index.php",
  "/setup.php",
  "/config.php",
  "/preferences.php",
);

$files = CMbPath::getPathTreeUnder("$root_dir/modules", $ignored, $extensions);
$count = CMbArray::countLeafs($files);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("files", $files);
$smarty->assign("count", $count);
$smarty->display("regression_checker.tpl");
