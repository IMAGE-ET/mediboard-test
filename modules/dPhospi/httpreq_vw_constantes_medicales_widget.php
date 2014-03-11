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

CCanDo::checkRead();

$context_guid = CValue::get('context_guid');
$context = CStoredObject::loadFromGuid($context_guid);

$ranks = CConstantesMedicales::getConstantsByRank('graph', false, CConstantesMedicales::guessHost($context));
$list_cste   = array();
$list_cumul = array();
$cste_nb = 0;
$cumul_nb = 0;
/* We only display constants with rank 1 */
if (array_key_exists(1, $ranks['all'])) {
  foreach ($ranks['all'][1] as $_cste) {
    if (substr($_cste, 0, 1) === "_") {
      continue;
    }
    /* We display at most 4 graph with cumuled constants */
    if (isset(CConstantesMedicales::$list_constantes[$_cste]['cumul_reset_config'])) {
      if ($cumul_nb < 4) {
        $list_cumul[] = $_cste;
        $cumul_nb++;
      }
      continue;
    }
    /* A most, we display only one graph with at most 5 constants */
    if ($cste_nb < 5) {
      $list_cste[] = $_cste;
      $cste_nb++;
    }
  }
}

// Global structure
$graphs_struct = array(
  "Constantes" => $list_cste,
);
foreach ($list_cumul as $_cumul) {
  $graphs_struct[CAppUI::tr("CConstantesMedicales-$_cumul-court")] = array($_cumul);
}

$where = array(
  'patient_id' => " = '$context->patient_id'",
  'context_class' => " = '$context->_class'",
  'context_id' => " = '$context->_id'",
);

$whereOr = array();
$constants_by_graph = array();
$i = 1;
foreach ($graphs_struct as $_name => $_fields) {
  if (empty($_fields)) {
    continue;
  }
  foreach ($_fields as $_field) {
    $whereOr[] = "$_field IS NOT NULL";
  }
  $constants_by_graph[$i] = array($_fields);
  $i++;
}
$graphs = array();

if (!empty($whereOr)) {
  $where[]  = implode(' OR ', $whereOr);
  $const = new CConstantesMedicales();
  $constants = array_reverse($const->loadList($where, 'datetime DESC', 10), true);

  $graph = CConstantesMedicales::formatGraphDatas($constants, CConstantesMedicales::guessHost($context), $constants_by_graph, true);
  unset($graph['min_x_index']);
  unset($graph['min_x_value']);
  unset($graph['drawn_constants']);

  /* Sorting the graphs data by tab name */
  foreach ($graph as $_key => $_graph) {
    if (($name = array_search($constants_by_graph[$_key][0], $graphs_struct)) !== false) {
      $graphs[$name] = $_graph[0];
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->display('inc_vw_constantes_medicales_widget.tpl');
