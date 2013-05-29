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

CCanDo::checkAdmin();
 
$name_canonical = CValue::post("name_canonical");
$name_short     = CValue::post("name_short");
$name_long      = CValue::post("name_long");
$license        = CValue::post("license");

$licenses = array(
  "GNU GPL" => "GNU General Public License, see http://www.gnu.org/licenses/gpl.html",
  "OXOL"    => "OXOL, see http://www.mediboard.org/public/OXOL",
);

$license = CValue::read($licenses, $license, $licenses["GNU GPL"]);

// Only alphanumeric caracters
$name_canonical = preg_replace("/[^\w\s]/", "", $name_canonical);
$name_short     = preg_replace("/[^\w\s]/", "", $name_short);
$name_long      = preg_replace("/[^\w\s]/", "", $name_long);

if (is_dir("modules/$name_canonical")) {
  CAppui::stepAjax("Module '$name_canonical' existe déjà", UI_MSG_ERROR);
}

$zip_path    = "dev/sample_module.zip";
$destination = "tmp/sample_module";

if (false == $files_count = CMbPath::extract($zip_path, $destination)) {
  CAppui::stepAjax("Impossible d'extraire l'archive '$zip_path'</div>", UI_MSG_ERROR);
}

rename("$destination/sample_module", "$destination/$name_canonical");

$path = "$destination/$name_canonical";

$files = array_merge(
  glob("$path/*"),
  glob("$path/classes/*"),
  glob("$path/locales/*"),
  glob("$path/templates/*")
);

$translate = array(
  '{NAME_CANONICAL}' => $name_canonical,
  '{NAME_SHORT}'     => $name_short,
  '{NAME_LONG}'      => $name_long,
  '{LICENSE}'        => $license,
);

foreach ($files as $_file) {
  if (is_dir($_file)) {
    continue;
  }
  
  file_put_contents(
    $_file,
    strtr(file_get_contents($_file), $translate)
  );
}

rename("$destination/$name_canonical", "modules/$name_canonical");

CAppUI::setMsg("Module '$name_canonical' créé", UI_MSG_OK);
CAppUI::js("location.reload()");

CApp::rip();
