<?php 

/**
 * $Id$
 *  
 * @category dPsante400
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$sejour_guid   = CValue::get("sejour_guid");
$patient_guid  = CValue::get("patient_guid");
/** @var CSejour $sejour */
$sejour        = CMbObject::loadFromGuid($sejour_guid);
/** @var CPatient $patient */
$patient       = CMbObject::loadFromGuid($patient_guid);

$sejour->loadNDA();
$patient->loadIPP();

$smarty = new CSmartyDP();
$smarty->assign("sejour" , $sejour);
$smarty->assign("patient", $patient);
$smarty->display("inc_edit_manually_ipp_nda.tpl");