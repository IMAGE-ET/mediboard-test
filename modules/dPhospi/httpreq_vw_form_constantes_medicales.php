<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
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

/** @var CGroups|CService|CRPU $host */
// On cherche le meilleur "herbegement" des constantes, pour charger les configuration adequat
if ($host_guid) {
  $host = CMbObject::loadFromGuid($host_guid);
}
else {
  $host = CConstantesMedicales::guessHost($context);
}

$show_cat_tabs = CConstantesMedicales::getHostConfig("show_cat_tabs", $host);
$show_enable_all_button = CConstantesMedicales::getHostConfig("show_enable_all_button", $host);

$dates = array();
if (!$selection) {
  $selection = CConstantesMedicales::getConstantsByRank('form', true, $host);
}
else {
  $selection = CConstantesMedicales::selectConstants($selection, 'form');
}

foreach (CConstantesMedicales::$list_constantes as $key => $cst) {
  $dates["$key"] = CMbDT::format(null, '%d/%m/%y');
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
$constantes->updateFormFields(); // Pour forcer le chargement des unités lors de la saisie d'une nouvelle constante

if ($context) {
  $constantes->patient_id    = $patient_id;
  $constantes->context_class = $context->_class;
  $constantes->context_id    = $context->_id;
}

$can_create = 0;
$modif_timeout = intval(CAppUI::conf("dPpatients CConstantesMedicales constants_modif_timeout", $host->_guid));
$msg_modif_timeout = '';
if (
    $constantes->_id &&
    $modif_timeout > 0 &&
    (time() - strtotime($constantes->datetime)) > ($modif_timeout * 3600)
) {
  $can_create = 1;
  $readonly = 1;
  $msg_modif_timeout = "Impossible de modifier cette saisie de constantes car elle a été saisie il y a plus de $modif_timeout heures.";
}

$patient_id = $constantes->patient_id ? $constantes->patient_id : $patient_id;
$latest_constantes = CConstantesMedicales::getLatestFor($patient_id, null, array(), $context, false);
// Création du template
$smarty = new CSmartyDP("modules/dPhospi");

$smarty->assign('constantes'            , $constantes);
$smarty->assign('latest_constantes'     , $latest_constantes);
$smarty->assign('context_guid'          , $context_guid);
$smarty->assign('readonly'              , $readonly);
$smarty->assign('selection'             , $selection);
$smarty->assign('dates'                 , $dates);
$smarty->assign('can_create'            , $can_create);
$smarty->assign('msg_modif_timeout'     , $msg_modif_timeout);
$smarty->assign('show_cat_tabs'         , $show_cat_tabs);
$smarty->assign('show_enable_all_button', $show_enable_all_button);
if ($tri) {
  $smarty->assign('real_context'        , CValue::get('real_context'));
  $smarty->assign('display_graph'       , CValue::get('display_graph', 0));
  $smarty->assign('tri'                 , $tri);
}

$smarty->display('inc_form_edit_constantes_medicales.tpl');

