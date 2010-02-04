<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage soins
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

// Plateau sélectionné
$plateau->load(CValue::getOrSessionAbs("plateau_id"));


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plateau", $plateau);
$smarty->assign("plateaux", $plateaux);
$smarty->display("vw_idx_plateau.tpl");


?>