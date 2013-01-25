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
$filter_class        = CValue::getOrSession("filter_class", "all");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modele_etiquette_id"   , $modele_etiquette_id);
$smarty->assign("classes"               , CCompteRendu::getTemplatedClasses());
$smarty->assign("filter_class"          , $filter_class);
$smarty->display("vw_etiquettes.tpl");
?>