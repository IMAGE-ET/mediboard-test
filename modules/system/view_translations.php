<?php
/**
 * Overwrite translation for the instance
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
global $locales;
$in_use_locales = $locales;

//load old locales
$locale = CAppUI::pref("LOCALE", "fr");
foreach (CAppUI::getLocaleFilesPaths($locale) as $_path) {
  include_once $_path;
}
$locales = array_filter($locales, "stringNotEmpty");
foreach ($locales as &$_locale) {
  $_locale = CMbString::unslash($_locale);
}

//get the list of translations made
$translation = new CTranslationOverwrite();
$translations_bdd = $translation->loadList();



/** @var CTranslationOverwrite[] $translations_bdd */
foreach ($translations_bdd as $_translation) {
  $_translation->loadOldTranslation($locales);
  $_translation->checkInCache();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("translations_bdd", $translations_bdd);
$smarty->display("view_translations.tpl");