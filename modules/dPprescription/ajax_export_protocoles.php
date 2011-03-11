<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ini_set("memory_limit", "256M");

$owner_type = CValue::get("owner_type");
$owner_id   = CValue::get("owner_id");

// Le nom du fichier va contenir la vue de l'objet
switch($owner_type) {
  case "praticien_id":
    $object = "CMediUsers";
    break;
  case "function_id":
    $object = "CFunctions";
    break;
  case "group_id":
    $object = "CGroups";
}

$object = new $object;
$object->load($owner_id);

$lower_bound = CValue::get("lower_bound") - 1;
$upper_bound = CValue::get("upper_bound") - 1;

// Chargement des ids des prescriptions selon l'object_class et l'id
$where = array();
$where[$owner_type] = " = '$owner_id'";
$where["object_id"] = " IS NULL";

$prescription = new CPrescription;
$prescriptions = $prescription->loadIds($where);


$doc = new CMbXMLDocument(null);
$root = $doc->createElement("protocoles");
$doc->appendChild($root);

$root_path = CAppUI::conf("root_dir");

foreach($prescriptions as $key => $_prescription) {
  if ($key < $lower_bound || $key > $upper_bound) continue;
  $protocole = CApp::fetch("dPprescription", "ajax_export_protocole", array("prescription_id" => $_prescription, "stream_xml" => 0));

  $doc_protocole = new CMbXMLDocument(null);
  $doc_protocole->loadXML($protocole);

  // Importation du noeud CPrescription
  $protocole_importe = $doc->importNode($doc_protocole->firstChild, true);

  // Ajout de ce noeud comme fils de protocoles
  $doc->documentElement->appendChild($protocole_importe);
}

$content = $doc->saveXML();
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="export-'.$object->_view.'-'.CValue::get("lower_bound").'-'.CValue::get("upper_bound").'.xml"');
header('Content-Length: '.strlen($content).';');

echo $content;
?>