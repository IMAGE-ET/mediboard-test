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

$allaitement_id = CValue::get("allaitement_id");
$patient_id     = CValue::getOrSession("patient_id");

$allaitement = new CAllaitement();
$allaitement->load($allaitement_id);

if (!$allaitement->_id) {
  $allaitement->patient_id = $patient_id;
}

$patient = new CPatient();
$patient->load($allaitement->patient_id);

$grossesses = $patient->loadRefsGrossesses();

$smarty = new CSmartyDP();

$smarty->assign("allaitement", $allaitement);
$smarty->assign("grossesses" , $grossesses);

$smarty->display("inc_edit_allaitement.tpl");