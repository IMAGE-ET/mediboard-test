<?php 

/**
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$autoadd_default = CAppUI::pref("AUTOADDSIGN", true);
$del = $_POST["del"];

// Sejour
// si on a un sejour et que l'option d'ajout automatique est activée
if (isset($_POST["_sejour_id"]) && $autoadd_default && ($_POST["_sejour_id"] != "")) {
  $doSejour = new CDoObjectAddEdit("CPathologie");

  // Ajout de l'antecedent dans le sejour
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($_POST["_sejour_id"], "CSejour");
  $doSejour->redirectStore = null;
  $doSejour->redirect = null;

  $doSejour->doIt();
}

$_POST["del"] = $del;

// Patient
$doPatient = new CDoObjectAddEdit("CPathologie");

if ($_POST["del"] != 1 && isset($_POST["_patient_id"])) {
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($_POST["_patient_id"], "CPatient");
}
$_POST["ajax"] = 1;

$doPatient->doIt();
