<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     Fabien Ménager <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$const_id     = CValue::get('const_id', 0);
$context_guid = CValue::get('context_guid');
$patient_id   = CValue::get('patient_id');
$readonly     = CValue::get('readonly');
$selection    = CValue::get('selection');
$tri          = CValue::get('tri', '');

$dates     = array();
if (!$selection) {
  //$selection = CConstantesMedicales::$list_constantes;
  $conf_constantes = explode("|", CConstantesMedicales::getConfig("important_constantes"));
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, array_flip($conf_constantes));
}
else {
  $selection_flip = array_flip($selection);
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, $selection_flip);
}

if ($tri) {
  foreach (CConstantesMedicales::$list_constantes as $key => $cst) {
    $dates["$key"] = mbTransformTime(null, null, '%d/%m/%y');
  }
  $patient = new CPatient();
  $patient->load($patient->_id);
  $const = $patient->loadRefConstantesMedicales();
  $const_id = $const[0]->_id;
}

$constantes = new CConstantesMedicales();
$constantes->load($const_id);
$constantes->loadRefContext();
$constantes->loadRefPatient();

if ($context_guid) {
  $context = CMbObject::loadFromGuid($context_guid);
  $constantes->patient_id = $patient_id;
  $constantes->context_class = $context->_class;
  $constantes->context_id = $context->_id;
}

$patient_id = $constantes->patient_id ? $constantes->patient_id : $patient_id;
$latest_constantes = CConstantesMedicales::getLatestFor($patient_id);
                 
// Création du template
$smarty = new CSmartyDP("modules/dPhospi");

$smarty->assign('constantes'    , $constantes);
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('context_guid'  , $context_guid);
$smarty->assign('readonly'      , $readonly);
$smarty->assign('selection'     , $selection);
if ($tri) {
  $smarty->assign('dates'         , $dates);
  $smarty->assign('real_context'	, CValue::get('real_context'));
  $smarty->assign('display_graph'	, CValue::get('display_graph', 0));
  $smarty->assign('tri'         	, $tri);
}
$smarty->display('inc_form_edit_constantes_medicales.tpl');

?>