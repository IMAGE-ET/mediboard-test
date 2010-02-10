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

// Plateaux disponibles
$plateau = new CPlateauTechnique;
$plateau->load(CValue::get("plateau_id"));
$plateau->loadRefsTechniciens();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plateau", $plateau);
$smarty->display("inc_repartition_plateau.tpl");
?>