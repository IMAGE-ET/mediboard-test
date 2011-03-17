<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkAdmin();

$bloc_id = CValue::getOrSession("bloc_id");

// Récupération des blocs de l'etablissement
$blocs_list = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// Récupération du bloc à modifier
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsNotes();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blocs_list", $blocs_list);
$smarty->assign("bloc",       $bloc);

$smarty->display("vw_idx_blocs.tpl");

?>