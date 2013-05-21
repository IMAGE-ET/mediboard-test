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

$owner_types = array(
  "CMediusers" => "prat",
  "CFunctions" => "func",
  "CGroups"    => "etab",
);

$modeles = CCompteRendu::loadAllModelesFor($object->_id, $owner_types[$object->_class], $filter_class, "body");

$link = new CModeleToPack();
$link->pack_id = $pack_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("link"   , $link);
$smarty->assign("modeles", $modeles);
$smarty->assign("pack_id", $pack_id);

$smarty->display("inc_form_modeles_links.tpl");
