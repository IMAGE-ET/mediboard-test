<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Fabien Ménager
*/

$_POST['dossier_medical_id'] = CDossierMedical::dossierMedicalId($_POST['_patient_id'], 'CPatient');

$do = new CDoObjectAddEdit('CEtatDent', 'etat_dent_id');
$do->doIt();

?>