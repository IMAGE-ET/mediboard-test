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
$host_guid    = CValue::get('host_guid');

$context = null;
if ($context_guid) {
  $context = CMbObject::loadFromGuid($context_guid);
}

$dates = array();
if (!$selection) {
  /** @var CGroups|CService|CRPU $host */

  // On cherche le meilleur "herbegement" des constantes, pour charger les configuration adequat
  if ($host_guid) {
    $host = CMbObject::loadFromGuid($host_guid);
  }
  else {
    $host = CConstantesMedicales::guessHost($context);
  }

  $selection = CConstantesMedicales::getConstantsByRank(true, $host);
}
else {
  $selection = CConstantesMedicales::selectConstants($selection);
}

foreach (CConstantesMedicales::$list_constantes as $key => $cst) {
  $dates["$key"] = CMbDT::transform(null, null, '%d/%m/%y');
}

if ($tri) {
  $patient = new CPatient();
  $patient->load($patient->_id);
  $const = $patient->loadRefConstantesMedicales();
  $const_id = $const[0]->_id;
}

$constantes = new CConstantesMedicales();
$constantes->load($const_id);
$constantes->loadRefContext();
$constantes->loadRefPatient();

if ($context) {
  $constantes->patient_id    = $patient_id;
  $constantes->context_class = $context->_class;
  $constantes->context_id    = $context->_id;
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
$smarty->assign('dates'         , $dates);
if ($tri) {
  $smarty->assign('real_context'	, CValue::get('real_context'));
  $smarty->assign('display_graph'	, CValue::get('display_graph', 0));
  $smarty->assign('tri'         	, $tri);
}

$smarty->display('inc_form_edit_constantes_medicales.tpl');

?>