<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

// Remove locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $sharedName = "locales-$localeName";
  
  if (!SHM::get($sharedName)) {
    CAppUI::stepAjax("Locales-shm-none", UI_MSG_OK, $localeName);
    continue;
  }
  
  if (!SHM::rem($sharedName)) {
    CAppUI::stepAjax("Locales-shm-rem-ko", UI_MSG_WARNING, $localeName);
    continue;
  }
  
  CAppUI::stepAjax("Locales-shm-rem-ok", UI_MSG_OK, $localeName);
}

// Don't generate locale files for all languages, will be generated when needed
//foreach (CAppUI::getAvailableLanguages() as $_language) {
//  CJSLoader::writeLocaleFile($_language);
//}
//CAppUI::stepAjax("Locales-javascript-cache-allup", UI_MSG_OK);

// Remove class paths
if (!SHM::get("class-paths")) {
  CAppUI::stepAjax("Classes-shm-none", UI_MSG_WARNING);
}
else {
  if (!SHM::rem("class-paths")) {
    CAppUI::stepAjax("Classes-shm-rem-ko", UI_MSG_WARNING);
  }
  
  CAppUI::stepAjax("Classes-shm-rem-ok", UI_MSG_OK);
}

// Remove modules cache
//if (!SHM::get("modules")) {
//  CAppUI::stepAjax("Modules-shm-none", UI_MSG_WARNING);
//}
//else {
//  if (!SHM::rem("modules")) {
//    CAppUI::stepAjax("Modules-shm-rem-ko", UI_MSG_ERROR);
//  }
//
//  CAppUI::stepAjax("Modules-shm-rem-ok", UI_MSG_OK);
//}

// Remove child classes cache
if (!SHM::get("child-classes")) {
  CAppUI::stepAjax("ChildClasses-shm-none", UI_MSG_OK);
}
else {
  if (!SHM::rem("child-classes")) {
    CAppUI::stepAjax("ChildClasses-shm-rem-ko", UI_MSG_WARNING);
  }
  
  CAppUI::stepAjax("ChildClasses-shm-rem-ok", UI_MSG_OK);
}

// Remove configuration model
if (!SHM::get("config-model")) {
  CAppUI::stepAjax("ConfigModel-shm-none", UI_MSG_OK);
}
else {
  if (!SHM::rem("config-model")) {
    CAppUI::stepAjax("ConfigModel-shm-rem-ko", UI_MSG_WARNING);
  }
  
  CAppUI::stepAjax("ConfigModel-shm-rem-ok", UI_MSG_OK);
}

// Remove configuration values
if (!SHM::get("config-values")) {
  CAppUI::stepAjax("ConfigValues-shm-none", UI_MSG_OK);
}
else {
  if (!SHM::rem("config-values")) {
    CAppUI::stepAjax("ConfigValues-shm-rem-ko", UI_MSG_WARNING);
  }
  
  CAppUI::stepAjax("ConfigValues-shm-rem-ok", UI_MSG_OK);
}

/////////// CSS cache
$css_files = glob("tmp/*.css");
foreach ($css_files as $_css_file) {
  unlink($_css_file);
}
CAppUI::stepAjax("CSS-cache-ok", UI_MSG_OK, count($css_files));

/////////// JavaScript cache
$js_files = glob("tmp/*.js");
foreach ($js_files as $_js_file) {
  unlink($_js_file);
}
CAppUI::stepAjax("JS-cache-ok", UI_MSG_OK, count($js_files));

////////// Smarty templates
// DO NOT use CMbPath::removed because it must be used in the installer
$templates = array_merge(glob("tmp/templates_c/*/*/*/*"), glob("tmp/templates_c/*/*/*"));
foreach ($templates as $_template) {
  if (is_file($_template)) {
    unlink($_template);
  }
}
$template_dirs = array_merge(glob("tmp/templates_c/*/*/*", GLOB_ONLYDIR), glob("tmp/templates_c/*/*", GLOB_ONLYDIR));
foreach ($template_dirs as $_dir) {
  rmdir($_dir);
}
CAppUI::stepAjax("template-cache-removed", UI_MSG_OK, count($templates));

////////// Module specific removals
foreach (glob("modules/*/empty_shared_memory.php") as $script) {
  include $script;
}