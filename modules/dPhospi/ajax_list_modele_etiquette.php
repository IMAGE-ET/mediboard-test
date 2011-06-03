<?php /* $Id: vw_idx_etiquette.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
CCanDo::checkEdit();

$filter_class = CValue::getOrSession("filter_class");
$modele_etiquette_id = CValue::getOrSession("modele_etiquette_id");

// Récupération de la liste suivant l'object_class
$modele_etiquette = new CModeleEtiquette;

$where = array();
$where["group_id"] = " = '" . CGroups::loadCurrent()->_id . "'";

if ($filter_class != "all")
  $where["object_class"] = " = '$filter_class'";
$liste_modele_etiquette = $modele_etiquette->loadList($where);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("filter_class"          , $filter_class);
$smarty->assign("modele_etiquette_id"   , $modele_etiquette_id);
$smarty->assign("classes"               , CCompteRendu::getTemplatedClasses());
$smarty->assign("liste_modele_etiquette", $liste_modele_etiquette);
$smarty->display("inc_list_modele_etiquette.tpl");
?>