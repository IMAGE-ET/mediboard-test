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

// Plateau du contexte
$plateau = new CPlateauTechnique;
$plateau->load(CValue::get("plateau_id"));
$plateau->loadRefsEquipements();

// Equipement à editer
$equipement = new CEquipement;
$equipement->load(CValue::get("equipement_id"));
$equipement->plateau_id = $plateau->_id;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("equipement", $equipement);
$smarty->assign("plateau", $plateau);

$smarty->display("inc_edit_equipement.tpl");


?>