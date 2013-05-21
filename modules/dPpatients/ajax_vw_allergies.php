<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_guid = CValue::get("object_guid");

// Chargement du patient
/** @var CPatient $patient */
$patient = CMbObject::loadFromGuid($object_guid);

// Chargement de son dossier médical
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;

// Chargement des allergies   
$allergies = array();
if ($dossier_medical->_id) {
  $dossier_medical->loadRefsAllergies();
  $allergies = $dossier_medical->_ref_allergies;
}

$keywords = explode("|", CAppUI::conf("soins ignore_allergies"));

foreach ($keywords as $_keyword) {
  foreach ($allergies as $_key => $_allergie) {
    if (preg_match('/^'.strtolower($_keyword).'$/', strtolower($_allergie->_view))) {
      unset($allergies[$_key]);
      break;
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("allergies", $allergies);
$smarty->display("inc_vw_allergies.tpl");
