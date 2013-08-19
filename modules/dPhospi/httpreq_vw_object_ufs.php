<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

// Récupération des paramètres
$object_guid = CValue::get("object_guid");

$object = CMbObject::loadFromGuid($object_guid);

$affectations_uf = $object->loadBackRefs("ufs");

$uf  = new CUniteFonctionnelle();
$uf->group_id = CGroups::loadCurrent()->_id;
$ufs = $uf->loadMatchingList('libelle');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("ufs", $ufs);
$smarty->assign("affectations_uf", $affectations_uf);

$smarty->display("httpreq_vw_object_ufs.tpl");
