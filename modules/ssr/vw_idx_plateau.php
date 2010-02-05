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

$plateau = new CPlateauTechnique;
$plateau->group_id = CGroups::loadCurrent()->_id;

// Plateaux disponible
$plateaux = $plateau->loadMatchingList();
foreach($plateaux as $_plateau) {
  $_plateau->countBackRefs("techniciens");  
  $_plateau->countBackRefs("equipements");  
}

// Plateau sélectionné
$plateau->load(CValue::getOrSession("plateau_id"));
$plateau->loadRefsTechniciens();
$plateau->loadRefsEquipements();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plateau", $plateau);
$smarty->assign("plateaux", $plateaux);
$smarty->display("vw_idx_plateau.tpl");


?>