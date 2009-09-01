<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$function_id = mbGetValueFromGetOrSession("function_id");
$pack_id = mbGetValueFromGet("pack_id");

$pack = new CPrescriptionProtocolePack();
$pack->load($pack_id);

$protocoles = CPrescription::getAllProtocolesFor($praticien_id, $function_id, null, $pack->object_class);

$prescription = new CPrescription();
$prescription->loadRefsLinesMedComments();
$prescription->loadRefsLinesElementsComments();
$prescription->countLinesMedsElements();
// Chargement des items (protocoles) du pack de protocoles
if($pack->_id){
  $pack->loadRefsPackItems();

  foreach($pack->_ref_protocole_pack_items as $_pack_item){
    $_pack_item->loadRefPrescription();
    $_prescription =& $_pack_item->_ref_prescription;
    
    if(!$prescription->object_class){
      $prescription->object_class = $_prescription->object_class;
    }
    
    // Merge des lignes de medicaments
    $_prescription->loadRefsLinesMedComments();
    if($_prescription->_ref_lines_med_comments){
      foreach($_prescription->_ref_lines_med_comments as $type => $lines_by_type){
	      foreach($lines_by_type as $_line){
	        if($_line->_class_name == "CPrescriptionLineMedicament"){
	          $_line->countSubstitutionsLines();
	        }
	        $prescription->_ref_lines_med_comments[$type][] = $_line;
	      }
	    }
    }
    // Merge des lignes d'elements
    $_prescription->loadRefsLinesElementsComments();
    if($_prescription->_ref_lines_elements_comments){
	    foreach($_prescription->_ref_lines_elements_comments as $chapitre => $lines_by_chapitre){
	      foreach($lines_by_chapitre as $categorie_id => $lines_by_categorie){
	        foreach($lines_by_categorie as $type => $lines_type){
	          foreach($lines_type as $_line_elt){
	            $prescription->_ref_lines_elements_comments[$chapitre][$categorie_id][$type][$_line_elt->_id] = $_line_elt;
	          }
	        }
	      }
	    }
    }
    // Merge des perfusions
    $_prescription->loadRefsPerfusions();
    if($_prescription->_ref_perfusions){
      foreach($_prescription->_ref_perfusions as $_perfusion){
        $_perfusion->countSubstitutionsLines();
        $_perfusion->loadRefsLines();
				$_perfusion->loadVoies();
        $prescription->_ref_perfusions[$_perfusion->_id] = $_perfusion;
      }
    }
    
    // Compteur de lignes
    $_prescription->countLinesMedsElements();
    foreach($_prescription->_counts_by_chapitre as $chapitre => $_count_by_chap){
      $prescription->_counts_by_chapitre[$chapitre] += $_count_by_chap;
    }
  }
}

$categories = CCategoryPrescription::loadCategoriesByChap();

// Chargement de l'utilisateur courant
$user = new CMediusers();
$user->load($AppUI->user_id);
$is_praticien = $user->isPraticien();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id"         , $praticien_id);
$smarty->assign("function_id"          , $function_id);
$smarty->assign("protocoles"           , $protocoles);
$smarty->assign("pack"                 , $pack);
$smarty->assign("prescription"         , $prescription);
$smarty->assign("class_category"       , new CCategoryPrescription());
$smarty->assign("is_praticien"         , $is_praticien);
$smarty->assign("mode_pack"            , 1);
$smarty->assign("today"                , mbDate());
$smarty->assign("now"                  , mbDateTime());
$smarty->assign("refresh_pharma"       , 0);
$smarty->assign("contexteType"         , "");
$smarty->assign("categories"           , $categories);
$smarty->display("inc_vw_pack_protocole.tpl");

?>