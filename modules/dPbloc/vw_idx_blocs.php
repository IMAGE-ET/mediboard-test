<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkAdmin();

$bloc_id  = CValue::getOrSession("bloc_id");
$salle_id = CValue::getOrSession("salle_id");
$poste_sspi_id = CValue::getOrSession("poste_sspi_id");

// Récupération des blocs de l'etablissement
$blocs_list = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// Récupération des postes de l'établissement
$postes_list = CGroups::loadCurrent()->loadPostes(PERM_EDIT);

// Récupération du bloc à à ajouter / modifier
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

if (CAppUI::conf("dPplanningOp COperation use_poste")) {
  $bloc->loadRefPoste();
}

// Récupération de la salle à ajouter / modifier
$salle = new CSalle();
$salle->load($salle_id);

// Récupération du poste à ajouter / modifier
$poste = new CPosteSSPI();
$poste->load($poste_sspi_id);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blocs_list" , $blocs_list);
$smarty->assign("postes_list", $postes_list);
$smarty->assign("bloc"       , $bloc);
$smarty->assign("salle"      , $salle);
$smarty->assign("poste"      , $poste);

$smarty->display("vw_idx_blocs.tpl");

?>