<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CCanDo::checkRead();

$selection = json_decode(stripslashes(CValue::get('constants', '[]')));

$patient_id = CValue::get('patient_id');
$context_guid = CValue::get('context_guid');
$period = CValue::get('period', 0);

if ($context_guid) {
  $context = CMbObject::loadFromGuid($context_guid);
}

$patient = new CPatient();
$patient->load($patient_id);

$where = array();

if ($context_guid) {
  $where['context_class'] = " = '$context->_class'";
  $where['context_id'] = " = $context->_id";
}
else {
  $context = 'all';
}

$where['patient_id'] = " = $patient->_id";

if ($period) {
  switch ($period) {
    case 'week':
      $where['datetime'] = " > '" . CMbDT::dateTime('-7 days') . "'";
      break;
    case 'month':
      $where['datetime'] = " > '" . CMbDT::dateTime('-1 month') . "'";
      break;
    case 'year':
      $where['datetime'] = " > '" . CMbDT::dateTime('-1 year') . "'";
      break;
    default:
  }
}

$whereOr = array();
foreach ($selection as $_constant) {
  $whereOr[] = "$_constant IS NOT NULL";
}

if (!empty($whereOr)) {
  $where[] = implode(' OR ', $whereOr);
}
$constant = new CConstantesMedicales();
$constants = $constant->loadList($where, 'datetime DESC');

$smarty = new CSmartyDP();

if (!empty($constants)) {
  $time = false;
  if ($period) {
    $time = true;
  }

  $graph = new CConstantGraph(CConstantesMedicales::guessHost($context), $context_guid, false, $time);

  $constants_by_graph = array(
    1 => array(
      $selection
    )
  );

  $graph->formatGraphDatas(array_reverse($constants, true), $constants_by_graph);

  $smarty->assign('graphs', array(1 => $graph->graphs[1][0]));
  $smarty->assign('min_x_index', $graph->min_x_index);
  $smarty->assign('min_x_value', $graph->min_x_value);
}
else {
  $smarty->assign('msg', CAppUI::tr('CConstantGraph-msg-no_values'));
}

$smarty->display('inc_custom_constants_graph.tpl');