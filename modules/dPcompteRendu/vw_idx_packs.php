<?php

/**
 * Interface des packs de documents
 *
 * @category DPcompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$filtre = new CPack();

$filtre->user_id      = CValue::getOrSession("user_id");
$filtre->function_id  = CValue::getOrSession("function_id");
$filtre->object_class = CValue::getOrSession("filter_class");

$filtre->loadRefOwner();

$classes = CCompteRendu::getTemplatedClasses();

$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_function");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("classes"        , $classes);
$smarty->assign("filtre"         , $filtre);
$smarty->assign("access_function", $access_function);

$smarty->display("vw_idx_packs.tpl");
