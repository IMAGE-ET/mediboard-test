<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $can;

$can->needsRead();

$examen = new CExamenLabo;
$examen->load(mbGetValueFromGet("examen_id"));

$patient = new CPatient;
$patient->load(mbGetValueFromGet("patient_id"));

$item = new CPrescriptionLaboExamen;
$resultats = $item->loadResults($patient->_id, $examen->_id, 20);

// Création du graph
$graph = new CResultatsLaboGraph($patient, $examen, $resultats);

$graph->Stroke();
?>
