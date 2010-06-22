<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

global $AppUI, $m, $tab;

$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour;
$sejour->load($sejour_id);

$lines = array();

// Bilan SSR  
$bilan = new CBilanSSR;
$bilan->sejour_id = $sejour->_id;
$bilan->loadMatchingObject();

// Prescription SSR
$prescription_SSR = new CPrescription();
$prescription_SSR->object_id = $sejour->_id;
$presctiption_SSR->object_class = "CSejour";
$prescription_SSR->type = "sejour";
$prescription_SSR->loadMatchingObject();

// Chargement des lignes de la prescription
if ($prescription_SSR->_id){
  $line = new CPrescriptionLineElement();
  $line->prescription_id = $prescription_SSR->_id;
  $_lines = $line->loadMatchingList("debut ASC");
  foreach($_lines as $_line){
    $lines[$_line->_ref_element_prescription->category_prescription_id][] = $_line;
  }
}


// Aides à la saisie
$sejour->loadAides($AppUI->user_id);

// Chargement des categories de prescription
$categories = array();
$category = new CCategoryPrescription();
$where[] = "chapitre = 'kine' OR chapitre = 'soin' OR chapitre = 'consult'";
$group_id = CGroups::loadCurrent()->_id;
$where[] = "group_id = '$group_id' OR group_id IS NULL";

$order = "nom";
$categories = $category->loadList($where, $order);

// Dossier médical visibile ?
$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $AppUI->_ref_user->isFromType(array("Infirmière"));

$can_edit_prescription = $AppUI->_ref_user->isPraticien() || $AppUI->_ref_user->isAdmin();

// Suppression des categories vides
if(!$can_edit_prescription){
  foreach($categories as $_cat_id => $_category){
    if(!array_key_exists($_cat_id, $lines)){
      unset($categories[$_cat_id]); 
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour"              , $sejour);
$smarty->assign("bilan"               , $bilan);
$smarty->assign("categories"          , $categories);
$smarty->assign("prescription_SSR"    , $prescription_SSR);
$smarty->assign("lines"               , $lines);
$smarty->assign("can_edit_prescription", $can_edit_prescription);
$smarty->display("inc_form_bilan_ssr.tpl");
?>