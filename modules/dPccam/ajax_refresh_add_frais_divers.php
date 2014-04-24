<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$object_guid = CValue::get("object_guid");

/* @var CCodable $object*/
$object = CMbObject::loadFromGuid($object_guid);
$object->loadRefsFraisDivers();

$frais_divers = new CFraisDivers();
$frais_divers->setObject($object);
$frais_divers->quantite = 1;
$frais_divers->coefficient = 1;
$frais_divers->num_facture = 1;
if ($object->_class == "CConsultation" && $object->valide) {
  $object->loadRefFacture();
  $frais_divers->num_facture = count($object->_ref_factures)+1;
}
$frais_divers->loadListExecutants();
$frais_divers->loadExecution();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object"      , $object);
$smarty->assign("frais_divers", $frais_divers);

$smarty->display("inc_form_add_frais_divers.tpl");
