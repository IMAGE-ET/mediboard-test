<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$lang = CValue::getOrSession("lang", CCodeCIM10::LANG_FR);

$code = CValue::getOrSession("code", "(A00-B99)");
$cim10 = new CCodeCIM10($code);
$cim10->load($lang);
$cim10->loadRefs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("lang" , $lang);
$smarty->assign("cim10", $cim10);

$smarty->display("vw_full_code.tpl");
