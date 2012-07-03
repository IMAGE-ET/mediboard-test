<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
CCanDo::checkRead();

$examen = new CExamenLabo;
$examen->load(CValue::get("examen_id"));

$patient = new CPatient;
$patient->load(CValue::get("patient_id"));

$item = new CPrescriptionLaboExamen;
$resultats = $item->loadResults($patient->_id, $examen->_id, 20);

// Création du graph
$graph = new CResultatsLaboGraph($patient, $examen, $resultats);

$graph->Stroke();
?>
