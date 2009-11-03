<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $prat;
$max = CValue::getOrSession("max", 30);

$ds = CSQLDataSource::get("std");

$ds->exec("CREATE TEMPORARY TABLE prat_patient (
  patient_id INT(11) UNSIGNED
)");

$ds->exec("INSERT INTO prat_patient (patient_id)
	SELECT patient_id 
	FROM sejour
	WHERE praticien_id = $prat->_id
");

$ds->exec("INSERT INTO prat_patient (patient_id)
	SELECT patient_id 
	FROM consultation, plageconsult
	WHERE consultation.plageconsult_id = plageconsult.chir_id
	AND plageconsult.chir_id = $prat->_id
");

$ds->exec("CREATE TEMPORARY TABLE patient_medecin (
  patient_id INT(11) UNSIGNED, 
  medecin_id INT(11) UNSIGNED
)");

$ds->exec("INSERT INTO patient_medecin (patient_id, medecin_id)
	SELECT DISTINCT(prat_patient.patient_id), patients.medecin_traitant
	FROM prat_patient, patients
	WHERE prat_patient.patient_id = patients.patient_id
  AND patients.medecin_traitant IS NOT NULL
");

$prescripteurs = $ds->loadHashList("SELECT medecin_id, COUNT(*) AS nb_patients
	FROM patient_medecin
	GROUP BY medecin_id
	ORDER BY nb_patients DESC
	LIMIT 0, $max
");

// Chargement des medecins trouvés
$medecin = new CMedecin();
$where = array();
$where["medecin_id"] = CSQLDataSource::prepareIn(array_keys($prescripteurs));
$medecins = $medecin->loadList($where);

// Variables de templates
$smarty = new CSmartyDP();

$smarty->assign("max", $max);
$smarty->assign("medecins", $medecins);
$smarty->assign("prescripteurs", $prescripteurs);

$smarty->display("vw_prescripteurs.tpl");

?>