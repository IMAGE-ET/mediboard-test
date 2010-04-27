<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCando::checkRead();

$date = CValue::getOrSession("date", mbDate());

$technicien = new CTechnicien;
$technicien->load(CValue::get("technicien_id"));

// Kine
$technicien->loadRefKine();
$kine = $technicien->_ref_kine;

$planning = new CPlanningWeek($date);
$planning->title = "Planning du technicien '$kine->_view'";
$planning->guid = $kine->_guid;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");


?>