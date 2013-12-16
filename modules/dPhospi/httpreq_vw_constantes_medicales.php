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

/**
 * Convert a string in a float value
 *
 * @param string|null $v The value
 *
 * @return float|null
 */
function getValue($v) {
  return ($v === null) ? null : floatval($v);
}

global $m;

$user = CMediusers::get();

if (
  !$user->isMedical() &&
  !CModule::getCanDo('soins')->read &&
  !CModule::getCanDo('dPurgences')->read &&
  !CModule::getCanDo('dPcabinet')->edit
) {
  CModule::getCanDo($m)->redirect();
}

$context_guid          = CValue::get('context_guid');
$selected_context_guid = CValue::get('selected_context_guid', $context_guid);
$patient_id            = CValue::get('patient_id');
$readonly              = CValue::get('readonly');
$selection             = CValue::get('selection');
$date_min              = CValue::get('date_min');
$date_max              = CValue::get('date_max');
$print                 = CValue::get('print');
$paginate              = CValue::get('paginate', 0);
$start                 = CValue::get('start', 0);
$count                 = CValue::get('count', 50);
$simple_view           = CValue::get('simple_view', 0);
$host_guid             = CValue::get('host_guid');

if (!$start) {
  $start = 0;
}

if ($paginate) {
  $limit = "$start,$count";
}
else {
  $limit = $count;
}

$current_context = null;
if ($context_guid) {
  $current_context = CMbObject::loadFromGuid($context_guid);
}

$custom_selection = $selection ? $selection : array();

/** @var CGroups|CService|CRPU $host */

// On cherche le meilleur "hebergement" des constantes, pour charger les configurations adequat
if ($host_guid) {
  $host = CMbObject::loadFromGuid($host_guid);
}
else {
  $host = CConstantesMedicales::guessHost($current_context);
}

$show_cat_tabs = CConstantesMedicales::getHostConfig("show_cat_tabs", $host);

if (!$selection || $selected_context_guid === 'all') {
  $selection = CConstantesMedicales::getConstantsByRank('form', $show_cat_tabs, $host);
}
else {
  $selection = CConstantesMedicales::selectConstants($selection);
}

// If print mode, no need to include hidden graphs
if ($print) {
  $selection["all"]["hidden"] = array();
}

$old_constants_to_draw = ($print == 1 ? $selection : CConstantesMedicales::$list_constantes);

$show_enable_all_button = CConstantesMedicales::getHostConfig("show_enable_all_button", $host);

$constants_to_draw = $selection;

/** @var CMbObject|CPatient|CSejour $context */
if ($selected_context_guid !== 'all') {
  $context = CMbObject::loadFromGuid($selected_context_guid);
}
else {
  $context = CMbObject::loadFromGuid($context_guid);
}

$context->loadRefs();

if ($context) {
  if ($context instanceof CPatient) {
    $patient = $context;
  }
  else {
    $patient = $context->_ref_patient;
  }
}

if ($patient_id) {
  $patient = new CPatient;
  $patient->load($patient_id);
}

if ($selected_context_guid == "all") {
  $context = null;
}

//CConstantesMedicales::$_latest_values = array();
$latest_constantes = $patient->loadRefConstantesMedicales(null, array(), $context, false);

$patient->loadRefPhotoIdentite();

$where = array(
  "patient_id" => " = '$patient->_id'"
);

// Construction d'une constante médicale
$constantes = new CConstantesMedicales();
$constantes->patient_id = $patient->_id;
$constantes->loadRefPatient();

// Les constantes qui correspondent (dans le contexte ou non)
$where_context = $where;
$where_context["context_class"] = "IS NOT NULL";
$where_context["context_id"] = "IS NOT NULL";

$query = new CRequest;
$query->addTable($constantes->_spec->table);
$query->addColumn("context_class");
$query->addColumn("context_id");
$query->addWhere($where_context);
$query->addGroup(array("context_class", "context_id"));

$query = $query->getRequest();
$list = $constantes->_spec->ds->loadList($query);
$list_contexts = array();

foreach ($list as $_context) {
  /** @var CMbObject $c */
  $c = new $_context["context_class"];
  $c = $c->getCached($_context["context_id"]);

  // Cas d'un RPU
  if ($c instanceof CConsultation && $c->sejour_id) {
    continue;
  }

  $c->loadRefsFwd();
  $list_contexts[$c->_guid] = $c;
}

if ($current_context instanceof CConsultation) {
  $current_context->loadComplete();
}

// Cas d'un RPU
if ($current_context instanceof CConsultation && $current_context->sejour_id) {
  $current_context->loadRefSejour();
  $current_context = $current_context->_ref_sejour;
  $current_context->loadComplete();
  $context = $current_context;
  $context_guid = $current_context->_guid;
}
if (!isset($list_contexts[$current_context->_guid])) {
  $current_context->loadRefsFwd();
  $list_contexts[$current_context->_guid] = $current_context;
}

if (!count($list_contexts)) {
  $list_contexts[] = $current_context;
}

if ($context && $selected_context_guid !== 'all') {
  $where["context_class"] = " = '$context->_class'";
  $where["context_id"] = " = '$context->_id'";

  // Needed to know if we are in the right context
  $constantes->context_class = $context->_class;
  $constantes->context_id = $context->_id;
  $constantes->loadRefContext();
}

$constantes->updateFormFields(); // Pour forcer le chargement des unités lors de la saisie d'une nouvelle constante

$whereOr = array();
foreach ($constants_to_draw as $_cat => $_ranks) {
  foreach ($_ranks as $rank => $_constants) {
    foreach ($_constants as $name) {
      if ($name[0] === "_") {
        continue;
      }
      $whereOr[] = "$name IS NOT NULL ";
    }
  }
}

if (!empty($whereOr)) {
  $where[] = implode(" OR ", $whereOr);
}

if ($date_min) {
  $where[] = "datetime >= '$date_min'";
}

if ($date_max) {
  $where[] = "datetime <= '$date_max'";
}

/** @var CConstantesMedicales[] $list_constantes */
// Les constantes qui correspondent (dans le contexte cette fois)
$list_constantes = $constantes->loadList($where, "datetime DESC", $limit);
$total_constantes = $constantes->countList($where);

$constantes_medicales_grid = CConstantesMedicales::buildGrid($list_constantes, false);

$const_ids = array();
foreach ($list_constantes as $_cst) {
  $const_ids[] = $_cst->_id;
}

$list_constantes = array_reverse($list_constantes, true);

$graphs_structure = CConstantesMedicales::sortConstantsbyGraph($list_constantes, $host);
$graphs_datas = CConstantesMedicales::formatGraphDatas($list_constantes, $host);

$min_x_index = $graphs_datas['min_x_index'];
$min_x_value = $graphs_datas['min_x_value'];
$drawn_constants = $graphs_datas['drawn_constants'];
unset($graphs_datas['min_x_index']);
unset($graphs_datas['min_x_value']);
unset($graphs_datas['drawn_constants']);

// On récupère dans tous les cas le poids et la taille du patient
$patient->loadRefConstantesMedicales(null, array("poids", "taille"), null, false);
//mbTrace($data);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('readonly',                   $readonly);
$smarty->assign('constantes',                 $constantes);
$smarty->assign('context',                    $context);
$smarty->assign('context_guid',               $context_guid);
$smarty->assign('list_contexts',              $list_contexts);
$smarty->assign('all_contexts',               $selected_context_guid == 'all');
$smarty->assign('patient',                    $patient);
$smarty->assign('const_ids',                  $const_ids);
$smarty->assign('latest_constantes',          $latest_constantes);
$smarty->assign('selection',                  $selection);
$smarty->assign('custom_selection',           $custom_selection);
$smarty->assign('print',                      $print);
$smarty->assign('graphs_datas',               $graphs_datas);
$smarty->assign('graphs_structure',            $graphs_structure);
$smarty->assign('min_x_index',                $min_x_index);
$smarty->assign('min_x_value',                $min_x_value);
$smarty->assign('drawn_constants',            $drawn_constants);
$smarty->assign('start',                      $start);
$smarty->assign('count',                      $count);
$smarty->assign('total_constantes',           $total_constantes);
$smarty->assign('paginate',                   $paginate);
$smarty->assign('constantes_medicales_grid',  $constantes_medicales_grid);
$smarty->assign('simple_view',                $simple_view);
$smarty->assign('show_cat_tabs',              $show_cat_tabs);
$smarty->assign('show_enable_all_button',     $show_enable_all_button);
$smarty->display('inc_vw_constantes_medicales.tpl');