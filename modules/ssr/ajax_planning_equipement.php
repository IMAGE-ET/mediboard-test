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

$equipement = new CEquipement;
$equipement->load(CValue::get("equipement_id", 33));

$planning = new CPlanningWeek;
$planning->title = "Planning de l'équipement '$equipement->_view'";
$planning->guid = $equipement->_guid;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");


?>