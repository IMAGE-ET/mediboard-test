<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;


$date = mbGetValueFromGetOrSession("date", mbDate()); 
$pathos = new CDiscipline();

$sejour_id = mbGetValueFromGet("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPraticien();
$sejour->loadRefPatient();
$sejour->getDroitsCMU();
    
$sejour->loadRefsOperations();
foreach($sejour->_ref_operations as &$operation) {
  $operation->loadRefsCodesCCAM();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("pathos",$pathos);
$smarty->assign("date" , $date);
$smarty->assign("curr_sejour" , $sejour);
$smarty->display("inc_pathologies.tpl");

?>