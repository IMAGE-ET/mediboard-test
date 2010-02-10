<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$technicien = new CTechnicien;
$technicien->load(CValue::get("technicien_id"));

$technicien->loadRefKine();

$planning = new CPlanningWeek;
$planning->title = "Planning du technicien '{$technicien->_ref_kine->_view}'";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");


?>