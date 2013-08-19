<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$affichage_patho = CValue::getOrSession("affichage_patho");
$date = CValue::getOrSession("date", CMbDT::date());
$pathos = new CDiscipline();

// Recuperation de l'id du sejour
$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPraticien();
$sejour->_ref_praticien->loadRefFunction();
$sejour->loadRefPatient();
    
$sejour->loadRefsOperations();
foreach ($sejour->_ref_operations as &$operation) {
  $operation->loadExtCodesCCAM();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pathos",$pathos);
$smarty->assign("date" , $date);
$smarty->assign("curr_sejour" , $sejour);
$smarty->assign("affichage_patho", $affichage_patho);
$smarty->display("inc_pathologies.tpl");

