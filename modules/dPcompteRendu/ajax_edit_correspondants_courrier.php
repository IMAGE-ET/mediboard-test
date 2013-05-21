<?php

/**
 * Modification des correspondants d'un document
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$object_guid     = CValue::get("object_guid");
$compte_rendu_id = CValue::get("compte_rendu_id");

$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);

$object = CMbObject::loadFromGuid($object_guid);

$compte_rendu->_ref_object = $object;
$compte_rendu->loadRefsCorrespondantsCourrierByTagGuid();

$destinataires = array();
CDestinataire::makeAllFor($object);
$destinataires = CDestinataire::$destByClass;

if (!isset($destinataires["CMedecin"])) {
  $destinataires["CMedecin"] = array();
}

// Fusion avec les correspondants ajoutés par l'autocomplete
$compte_rendu->mergeCorrespondantsCourrier($destinataires);

$empty_corres = new CCorrespondantCourrier();
$empty_corres->valueDefaults();

$patient = new CPatient();

if (CDestinataire::$_patient != null) {
  $patient = CDestinataire::$_patient;
}

$smarty = new CSmartyDP;

$smarty->assign("compte_rendu" , $compte_rendu);
$smarty->assign("destinataires", $destinataires);
$smarty->assign("empty_corres" , $empty_corres);
$smarty->assign("patient"      , $patient);

$smarty->display("inc_edit_correspondants_courrier.tpl");
