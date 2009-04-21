<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $dPconfig;

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

$can->needsRead();

$mbPrescription = new CPrescriptionLabo();
$doc = new CMbXMLDocument();
$docReference = new CMbXMLDocument();
$docReference->load("modules/dPlabo/remote/prescription.xml");

$docReference->setSchema("modules/dPlabo/remote/prescription.xsd");
$doc->setSchema("modules/dPlabo/remote/prescription.xsd");
if (!$doc->checkSchema()) {
  return;
}

// Chargement de la prescription
$mb_prescription_id = dPgetParam($_POST, "mb_prescription_id", 2);
if ($mbPrescription->load($mb_prescription_id)) {
  $mbPrescription->loadRefs();
}

$doc->setDocument("tmp/Prescription-".$mbPrescription->_id.".xml");

$prescription    = $doc->addElement($doc, "prescription");
$doc->addAttribute($prescription, "id"  , $mbPrescription->_id);
$doc->addAttribute($prescription, "date", mbDate());
$nomPraticien    = $doc->addElement($prescription, "nomPraticien"   , $mbPrescription->_ref_praticien->_user_last_name);
$prenomPraticien = $doc->addElement($prescription, "prenomPraticien", $mbPrescription->_ref_praticien->_user_first_name);
$nomPatient      = $doc->addElement($prescription, "nomPatient"     , $mbPrescription->_ref_patient->nom);
$prenomPatient   = $doc->addElement($prescription, "prenomPatient"  , $mbPrescription->_ref_patient->prenom);
$date            = $doc->addElement($prescription, "date"           , mbDate($mbPrescription->date));
$analyses       = $doc->addElement($prescription, "analyses");
foreach($mbPrescription->_ref_examens as $curr_analyse) {
  $analyse = $doc->addElement($analyses, "analyse");
  $doc->addAttribute($analyse, "id", $curr_analyse->_id);
  $identifiant = $doc->addElement($analyse, "identifiant", $curr_analyse->identifiant);
  $libelle     = $doc->addElement($analyse, "libelle"    , $curr_analyse->libelle);
}

$docReference->schemaValidate();
$doc->schemaValidate();

$doc->addFile($mbPrescription);

?>