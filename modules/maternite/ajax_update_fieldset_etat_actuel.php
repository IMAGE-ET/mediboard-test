<?php 

/**
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$patient_id = CValue::get("patient_id");

$patient = new CPatient();
$patient->load($patient_id);

$patient->loadLastGrossesse();
$patient->loadLastAllaitement();

$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);

$smarty->display("inc_fieldset_etat_actuel.tpl");