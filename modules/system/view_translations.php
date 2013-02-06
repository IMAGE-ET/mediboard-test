<?php

/**
 * Overwrite translation for the instance
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

global $locales;
$cache_to_reload = false;

//get the list of translations made
$translation = new CTranslationOverwrite();
$translations_bdd = $translation->loadList();

//check for cache and old translation
foreach ($translations_bdd as $_trad_bdd ) {
  $_trad_bdd->loadOldTranslation();
  if (CAppUI::tr($_trad_bdd->source) != $_trad_bdd->translation) {
    $cache_to_reload = true;
    continue;
  }
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("translations_bdd", $translations_bdd);
$smarty->assign("cache",            $cache_to_reload);
$smarty->assign("locales",          $locales);
$smarty->display("view_translations.tpl");