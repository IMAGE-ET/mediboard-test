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
$traduction_id = CValue::getOrSession("trad_id");

$translation = new CTranslationOverwrite();
$translation->load($traduction_id);


//smarty
$smarty = new CSmartyDP();
$smarty->assign("translation", $translation);
$smarty->display("inc_edit_translation.tpl");