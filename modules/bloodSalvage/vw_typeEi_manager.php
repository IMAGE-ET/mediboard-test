<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$type_ei_id = mbGetValueFromGetOrSession("type_ei_id");

$type_ei = new CTypeEi();

$type_ei_list = $type_ei->loadlist();
$type_ei->loadRefs();

if($type_ei_id) {
  $type_ei = new CTypeEi();
  $type_ei->load($type_ei_id);
}

// Liste des Catégories
$firstdiv = null;

if(!$type_ei->_ref_evenement){
  $type_ei->_ref_evenement = array();
}

$listCategories = new CEiCategorie;
$listCategories = $listCategories->loadList(null, "nom");
foreach ($listCategories as $key=>$value){
  if($firstdiv===null){
    $firstdiv = $key;
  }
  $listCategories[$key]->loadRefsBack();
  $listCategories[$key]->checked = null;
  foreach($listCategories[$key]->_ref_items as $keyItem=>$valueItem){
    if(in_array($keyItem,$type_ei->_ref_evenement)){
      $listCategories[$key]->_ref_items[$keyItem]->checked = true;
      if($listCategories[$key]->checked){
        $listCategories[$key]->checked .= "|". $keyItem;
      }else{
        $listCategories[$key]->checked = $keyItem;
      }
    }else{
      $listCategories[$key]->_ref_items[$keyItem]->checked = false;
    }
  }
}

$smarty = new CSmartyDP();


$smarty->assign("type_ei",$type_ei);
$smarty->assign("type_ei_list",$type_ei_list);
$smarty->assign("firstdiv"       , $firstdiv);
$smarty->assign("listCategories" , $listCategories);
$smarty->display("vw_typeEi_manager.tpl");
?>