<?php /* $Id: vw_idx_etiquette.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$modele_etiquette_id = CValue::getOrSession("modele_etiquette_id");

$modele_etiquette = new CModeleEtiquette;
$group_id = CGroups::loadCurrent()->_id;

if ($modele_etiquette_id) {
  $modele_etiquette->load($modele_etiquette_id);
  $modele_etiquette->loadRefsNotes();
}

// Nouveau modèle d'étiquette dans le cas d'un changement d'établissement
if (!$modele_etiquette_id || $modele_etiquette->group_id != $group_id) {
  // Chargement des valeurs par défaut si pas de modele_etiquette_id
  $modele_etiquette = new CModeleEtiquette;
  $modele_etiquette->valueDefaults();
  $modele_etiquette->group_id = $group_id;
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modele_etiquette"   , $modele_etiquette);
$smarty->assign("classes"            , CCompteRendu::getTemplatedClasses());
$smarty->assign("fields"             , CModeleEtiquette::$fields);
$smarty->assign("listfonts"          , CModeleEtiquette::$listfonts);
$smarty->display("inc_edit_modele_etiquette.tpl");
?>