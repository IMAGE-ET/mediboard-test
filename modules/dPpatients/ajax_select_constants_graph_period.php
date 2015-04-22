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

$selection = json_decode(stripslashes(CValue::get('constants', '[]')));
$patient_id = CValue::get('patient_id');
$context_guid = CValue::get('context_guid');
$period = CValue::get('period', 'month');

$smarty = new CSmartyDP();
$smarty->assign('patient_id', $patient_id);
$smarty->assign('context_guid', $context_guid);
$smarty->assign('constants', json_encode($selection));
$smarty->assign('period', $period);
$smarty->display('inc_select_constants_graph_period.tpl');