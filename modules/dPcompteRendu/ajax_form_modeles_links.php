<?php

/**
 * Ajout de modèles à un pack de modèles
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object_guid  = CValue::get("object_guid");
$pack_id      = CValue::get("pack_id");
$filter_class = CValue::get("filter_class");

$object = CMbObject::loadFromGuid($object_guid, true);

$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_function");
$access_group    = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_group");

$owner_types = array(
  "CMediusers" => "prat",
  "CFunctions" => "func",
  "CGroups"    => "etab",
);

$modeles = CCompteRendu::loadAllModelesFor($object->_id, $owner_types[$object->_class], $filter_class, "body");
$nb_modeles = count($modeles["prat"]);
$nb_modeles += isset($modeles["func"]) ? count($modeles["func"]) : 0;
$nb_modeles += isset($modeles["etab"]) ? count($modeles["etab"]) : 0;

$link = new CModeleToPack();
$link->pack_id = $pack_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("link"            , $link);
$smarty->assign("modeles"         , $modeles);
$smarty->assign("pack_id"         , $pack_id);
$smarty->assign("access_function" , $access_function);
$smarty->assign("access_group"    , $access_group);
$smarty->assign("nb_modeles"      , $nb_modeles);

$smarty->display("inc_form_modeles_links.tpl");
