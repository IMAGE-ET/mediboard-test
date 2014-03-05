<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$constantes = new CConstantesMedicales();
$perms = $constantes->canDo();
if (!$perms->read) {
  $perms->redirect();
}

$const_id      = CValue::get('const_id', 0);
$context_guid  = CValue::get('context_guid');
$patient_id    = CValue::get('patient_id');
$can_edit      = CValue::get('can_edit');
$selection     = CValue::get('selection');
$host_guid     = CValue::get('host_guid');
$display_graph = CValue::get('display_graph', 1);
$tri_rpu       = CValue::get('tri_rpu', '');

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

$modif_timeout = intval(CAppUI::conf("dPpatients CConstantesMedicales constants_modif_timeout", $host->_guid));
$can_create = $perms->edit;
if ($perms->edit && $constantes->_id && $modif_timeout > 0 &&  (time() - strtotime($constantes->datetime)) > ($modif_timeout * 3600)) {
  $can_edit = 0;
}
else {
  $modif_timeout = 0;
}

/* Gestion des droits d'edition sur les constantes */
if (is_null($can_edit)) {
  /* Impossible d'éditer si on est pas dans le contexte actuel */
  if ($constantes->_id && $context_guid != $constantes->_ref_context->_guid) {
    $can_edit = 0;
  }
  else {
    $can_edit = $perms->edit;
  }
}

if (!$constantes->_id && !$constantes->datetime) {
  $constantes->datetime = CMbDT::dateTime();
}
$patient_id = $constantes->patient_id ? $constantes->patient_id : $patient_id;
$latest_constantes = CConstantesMedicales::getLatestFor($patient_id, null, array(), $context, false);
// Création du template
$smarty = new CSmartyDP();

$smarty->assign('constantes'            , $constantes);
$smarty->assign('latest_constantes'     , $latest_constantes);
$smarty->assign('context_guid'          , $context_guid);
$smarty->assign('selection'             , $selection);
$smarty->assign('dates'                 , $dates);
$smarty->assign('can_create'            , $can_create);
$smarty->assign('can_edit'              , $can_edit);
$smarty->assign('modif_timeout'         , $modif_timeout);
$smarty->assign('display_graph'         , $display_graph);
$smarty->assign('tri_rpu'               , $tri_rpu);
$smarty->assign('show_cat_tabs'         , $show_cat_tabs);
$smarty->assign('show_enable_all_button', $show_enable_all_button);

$smarty->display('inc_form_edit_constantes_medicales.tpl');

