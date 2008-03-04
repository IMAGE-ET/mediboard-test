<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromPost("prescription_id");
$protocole_id = mbGetValueFromPost("protocole_id");

// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);
$protocole->loadRefsLines();
$protocole->loadRefsLinesElement();

foreach($protocole->_ref_prescription_lines as $line){
  $new_line = new CPrescriptionLine();
  $new_line = $line;
  $new_line->_id = "";
  $new_line->prescription_id = $prescription_id;
  $new_line->store();
}

foreach($protocole->_ref_prescription_lines_element as $line_element){
  $new_line_element = new CPrescriptionLineElement();
  $new_line_element = $line_element;
  $new_line_element->_id = "";
  $new_line_element->prescription_id = $prescription_id;
  $new_line_element->store();
}

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reload($prescription_id)</script>";
exit();   
?>