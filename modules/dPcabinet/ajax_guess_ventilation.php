<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$consult_id = CValue::get("consult_id");
$patient_id = CValue::get("patient_id");

$consult_anesth = new CConsultAnesth();
$consult_anesth->load($consult_id);


$patient = new CPatient();
$patient->load($patient_id);

$constantes = reset($patient->loadRefConstantesMedicales(null, array("poids", "taille")));

$consult_anesth->plus_de_55_ans = $patient->_annees > 55 ? 1 : 0;
$consult_anesth->imc_sup_26 = $constantes->_imc > 26 ? 1 : 0;

$smarty = new CSmartyDP();

$smarty->assign("consult_anesth", $consult_anesth);

$smarty->display("inc_guess_ventilation.tpl");
