<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$results = CValue::post("results", array());
$results = html_entity_decode($results);
$results = json_decode(stripcslashes($results), true);

$csvName = "export_recherches_".CMbDT::format(null, "%d_%m_%Y_%H_%M_%S");
$csv = new CCSVFile();

$title = array("contexte", "patient", "type", "auteur", "praticien", "titre", "contenu");
$csv->writeLine($title);

foreach ($results as $result) {
  $object_contexte = CMbObject::loadFromGuid($result['_source']['object_ref_class']."-".$result['_source']['object_ref_id']);
  $contexte = $object_contexte->_view;
  $type = mb_convert_encoding(CAppUI::tr($result['_type']), "UTF-8", "WINDOWS-1252");
  $praticien = CMbObject::loadFromGuid("CMediusers-".$result["_source"]["prat_id"]);
  $auteur  = CMbObject::loadFromGuid("CMediusers-".$result["_source"]["author_id"]);
  $titre = $result["_source"]["title"];
  $contenu = $result["_source"]["body"];
  $patient = CMbObject::loadFromGuid("CPatient-".$result["_source"]["patient_id"]);
  $line = array($contexte, $patient->_view, $type, $auteur->_view, $praticien->_view , $titre, $contenu);
  $csv->writeLine($line);
}

$csv->stream($csvName);
return;