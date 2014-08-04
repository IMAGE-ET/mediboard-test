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

/** @var CAffectationUniteFonctionnelle[] $affectations_uf */
$affectations_uf = $object->loadBackRefs("ufs");

CMbObject::massLoadFwdRef($affectations_uf, "uf_id");

$ufs_selected = array(
  "medicale"    => false,
  "hebergement" => false,
  "soins"       => false,
);

foreach ($affectations_uf as $key => $_affectation_uf) {
  $_affectation_uf->loadRefUniteFonctionnelle();
  //Utilisation d'un seul type d'uf par service/chambre/lit?
  $ufs_selected[$_affectation_uf->_ref_uf->_id] = true;
  $ufs_selected[$_affectation_uf->_ref_uf->type] = true;
}

$ufs = CUniteFonctionnelle::getUFs($object->_class);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("ufs"   , $ufs);
$smarty->assign("affectations_uf", $affectations_uf);
$smarty->assign("ufs_selected", $ufs_selected);

$smarty->display("inc_affectation_uf.tpl");
