<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;

$affichage_patho = CValue::getOrSession("affichage_patho");
$date = CValue::getOrSession("date", mbDate()); 
$pathos = new CDiscipline();

// Recuperation de l'id du sejour
$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPraticien();
$sejour->_ref_praticien->loadRefFunction();
$sejour->loadRefPatient();
    
$sejour->loadRefsOperations();
foreach($sejour->_ref_operations as &$operation) {
  $operation->loadExtCodesCCAM();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pathos",$pathos);
$smarty->assign("date" , $date);
$smarty->assign("curr_sejour" , $sejour);
$smarty->assign("affichage_patho", $affichage_patho);
$smarty->display("inc_pathologies.tpl");

?>