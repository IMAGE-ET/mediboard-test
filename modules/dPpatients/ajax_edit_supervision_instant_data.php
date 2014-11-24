<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$supervision_instant_data_id = CValue::getOrSession("supervision_instant_data_id");

$instant_data = new CSupervisionInstantData();
$instant_data->load($supervision_instant_data_id);
$instant_data->loadRefsNotes();

if (!$instant_data->_id) {
  $instant_data->size = 11;
}

$smarty = new CSmartyDP();
$smarty->assign("instant_data", $instant_data);
$smarty->display("inc_edit_supervision_instant_data.tpl");
