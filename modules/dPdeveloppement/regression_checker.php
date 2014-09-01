<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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
  "/configuration.php",
  "/preferences.php",
);

$views = CMbPath::getPathTreeUnder("$root_dir/modules", $ignored, $extensions);
$count = CMbArray::countLeafs($views);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("views", $views);
$smarty->assign("count", $count);
$smarty->display("regression_checker.tpl");
