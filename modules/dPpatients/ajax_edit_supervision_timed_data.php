<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$supervision_timed_data_id = CValue::getOrSession("supervision_timed_data_id");

$timed_data = new CSupervisionTimedData();
$timed_data->load($supervision_timed_data_id);
$timed_data->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("timed_data",  $timed_data);
$smarty->display("inc_edit_supervision_timed_data.tpl");
