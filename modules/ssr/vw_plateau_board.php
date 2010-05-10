<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$plateau = new CPlateauTechnique;
$plateau->group_id = CGroups::loadCurrent()->_id;

// Plateaux disponibles
$plateaux_ids = array();
$plateaux = $plateau->loadMatchingList();
foreach ($plateaux as $_plateau) {
  $_plateau->loadBackRefs("equipements");
	$plateaux_ids[$_plateau->_id] = array_keys($_plateau->_back["equipements"]); 
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plateaux", $plateaux);
$smarty->assign("plateaux_ids", $plateaux_ids);
$smarty->display("vw_plateau_board.tpl");


?>