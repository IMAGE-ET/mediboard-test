<?php 

/**
 * Tooltip des antÚcÚdents du patient
 *  
 * @category dPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$dossier_medical_id = CValue::get("dossier_medical_id");
$type               = CValue::get("type");

$dossier_medical = new CDossierMedical();
$dossier_medical->load($dossier_medical_id);

if ($type) {
  $dossier_medical->loadRefsAntecedentsOfType($type);
}
else {
  $dossier_medical->loadRefsAntecedents();
}

$smarty = new CSmartyDP();

$smarty->assign("antecedents", $dossier_medical->_ref_antecedents_by_type);
$smarty->assign("type"       , $type);

$smarty->display("inc_tooltip_atcd.tpl");