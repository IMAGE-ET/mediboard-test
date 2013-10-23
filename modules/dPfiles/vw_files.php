<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$keywords      = CValue::getOrSession("keywords", null);
$selClass      = CValue::getOrSession("selClass", null);
$selKey        = CValue::getOrSession("selKey"  , null);
$typeVue       = CValue::getOrSession("typeVue" , 0);
$file_id       = CValue::get("file_id"          , null);
$accordDossier = CValue::get("accordDossier"    , 0);

$object = new CMbObject();

$file = new CFile();
$file->load($file_id);

// Chargement de l'objet
if ($selClass && $selKey) {
  /** @var CMbObject $object */
  $object = new $selClass;
  $object->load($selKey);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selClass"     , $selClass);
$smarty->assign("selKey"       , $selKey);
$smarty->assign("selView"      , $object->_view);
$smarty->assign("keywords"     , $keywords);
$smarty->assign("object"       , $object);
$smarty->assign("file"         , $file);
$smarty->assign("typeVue"      , $typeVue);
$smarty->assign("accordDossier", $accordDossier);

$smarty->display("vw_files.tpl");

