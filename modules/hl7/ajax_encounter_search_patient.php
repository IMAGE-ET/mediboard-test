<?php 

/**
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$patient_id = CValue::getOrSession("patient_id");

$patient = new CPatient();
$patient->load($patient_id);
$patient->loadIPP();

$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->display("inc_search_encounter.tpl");