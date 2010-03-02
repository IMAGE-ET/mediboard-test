<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsAdmin();

$salle_id = CValue::getOrSession("salle_id");

// Récupération des salles
$blocs_list = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// Récupération de la salle à ajouter/editer
$salle = new CSalle;
$salle->load($salle_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blocs_list", $blocs_list);
$smarty->assign("salle",      $salle);

$smarty->display("vw_idx_salles.tpl");

?>