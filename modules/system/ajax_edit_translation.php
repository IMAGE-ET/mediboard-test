<?php

/**
 * Edit Translation
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
CCanDo::checkEdit();
$translation_id = CValue::getOrSession("trad_id");
$language = CValue::getOrSession("language", "fr");
$languages = CAppUI::getAvailableLanguages();

$translation = new CTranslationOverwrite();
$translation->load($translation_id);


//smarty
$smarty = new CSmartyDP();
$smarty->assign("translation", $translation);
$smarty->assign("language", $language);
$smarty->assign("languages", $languages);
$smarty->display("inc_edit_translation.tpl");