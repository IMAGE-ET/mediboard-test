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

// Sejour SSR
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();

// Bilan SSR  
$bilan = new CBilanSSR;
$bilan->sejour_id = $sejour->_id;
$bilan->loadMatchingObject();

// Plateau
$plateau = new CPlateauTechnique;
$plateau->load('2');
$plateau->loadRefsTechniciens();
$plateau->loadRefsEquipements();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("bilan", $bilan);
$smarty->assign("sejour", $sejour);
$smarty->assign("plateau", $plateau);
$smarty->display("inc_planner_sejour.tpl");
?>