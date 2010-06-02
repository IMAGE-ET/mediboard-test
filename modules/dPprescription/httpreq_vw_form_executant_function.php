<?php /* $Id:  $ */


/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$function_category_prescription_id = CValue::getOrSession("function_category_prescription_id");
$category_id = CValue::getOrSession("category_id");

// Chargement de la liste des fonctions
$function = new CFunctions();
$functions = $function->loadListWithPerms(PERM_READ, null, "text");

$executant = new CFunctionCategoryPrescription();
$executant->load($function_category_prescription_id);
$executant->loadRefFunction();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("functions", $functions);
$smarty->assign("executant", $executant);
$smarty->assign("category_id", $category_id);
$smarty->display("inc_form_executant_function.tpl");

?>