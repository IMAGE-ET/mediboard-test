<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsEdit();

$compte_rendu_id = CValue::get("compte_rendu_id"   , 0);
$modele_id       = CValue::get("modele_id"         , 0);
$praticien_id    = CValue::get("praticien_id"      , 0);
$type            = CValue::get("type"              , 0);
$pack_id         = CValue::get("pack_id"           , 0);
$object_id       = CValue::get("object_id"         , 0);
$target_id       = CValue::get("target_id");
$target_class    = CValue::get("target_class");

// Faire ici le test des différentes variables dont on a besoin

$compte_rendu = new CCompteRendu;

// Modification d'un document
if ($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
  $compte_rendu->loadContent();
}

// Création à partir d'un modèle
else {
	$header = null;
	$footer = null;
	
  $compte_rendu->load($modele_id);
  $compte_rendu->loadContent();
  $compte_rendu->_id = null;
  $compte_rendu->chir_id = $praticien_id;
  $compte_rendu->function_id = null;
  $compte_rendu->object_id = $object_id;
  $compte_rendu->_ref_object = null;
  
  // Utilisation des headers/footers
  if ($compte_rendu->header_id || $compte_rendu->footer_id) {
    $compte_rendu->loadComponents();
    
		$header = $compte_rendu->_ref_header;
    $footer = $compte_rendu->_ref_footer;
  }
  
  // On fournit la cible
  if ($target_id && $target_class){
    $compte_rendu->object_id = $target_id;
    $compte_rendu->object_class = $target_class;
  }
  
  // A partir d'un pack
  if ($pack_id) {

    $pack = new CPack;
    $pack->load($pack_id);
    $compte_rendu->nom = $pack->nom;
    $compte_rendu->object_class = $pack->object_class;
    $compte_rendu->_source = $pack->_source;
    
    // Parcours des modeles du pack pour trouver le premier header et footer
    foreach($pack->_modeles as $mod) {
    	if ($mod->header_id || $mod->footer_id) {
    		$mod->loadComponents();
    	}
    	if (!isset($header)) $header = $mod->_ref_header;
    	if (!isset($footer)) $footer = $mod->_ref_footer;
    	if ($header && $footer) break;
    }

    // Marges et format
    $first_modele = reset($pack->_modeles);
    $compte_rendu->margin_top    = $first_modele->margin_top;
    $compte_rendu->margin_left   = $first_modele->margin_left;
    $compte_rendu->margin_right  = $first_modele->margin_right;
    $compte_rendu->margin_bottom = $first_modele->margin_bottom;
    $compte_rendu->page_height   = $first_modele->page_height;
    $compte_rendu->page_width    = $first_modele->page_width;

  }
  
  if ($header || $footer) {
  	$header->height = isset($header->height) ? $header->height : 20;
  	$footer->height = isset($footer->height) ? $footer->height : 20;
  	
    $style = "
      <style type='text/css'>
      #header {
        height: {$header->height}px;
        /*DOMPDF top: 0;*/
      }

      #footer {
        height: {$footer->height}px;
        /*DOMPDF bottom: 0;*/
      }";
    
    if ($header->_id) {
    	$header->loadContent();
      $header->_source = "<div id='header'>$header->_source</div>";
      
      if(CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 0) {      
        $header->height += 20;
      }
      $compte_rendu->header_id = null;
    }
    
    if ($footer->_id) {
      $footer->loadContent();
      $footer->_source = "<div id='footer'>$footer->_source</div>";

      if(CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 0) {
        $footer->height += 20;
      }
      $compte_rendu->footer_id = null;
    }
    
    $style.= "
      @media print { 
        #body { 
          padding-top: {$header->height}px;
        }
        hr.pagebreak {
          padding-top: {$header->height}px;
        }
      }";
    
    $style .="
      @media dompdf {
        #body {
          padding-bottom: {$footer->height}px;
        }
        hr.pagebreak {
          padding-top: 0px;
        }
      }</style>";
    
    $compte_rendu->_source = "<div id='body'>$compte_rendu->_source</div>";
    $compte_rendu->_source = $style . $header->_source . $footer->_source . $compte_rendu->_source;
  }

  $compte_rendu->updateFormFields();
  
}

$compte_rendu->loadRefsFwd();
$compte_rendu->_ref_object->loadRefsFwd();
$object =& $compte_rendu->_ref_object;

// Calcul du user concerné
$user = new CMediusers;
$user->_id = $AppUI->user_id;

if ($object instanceof CConsultAnesth) {
  $user->_id = $object->_ref_consultation->_praticien_id;
}

if ($object instanceof CCodable) {
  $user->_id = $object->_praticien_id;
}

$user->load();
$user->loadRefFunction();

// Chargement des catégories
$listCategory = CFilesCategory::listCatClass($compte_rendu->object_class);

// Gestion du template
$templateManager = new CTemplateManager($_GET);

$object->fillTemplate($templateManager);
$templateManager->document = $compte_rendu->_source;
$templateManager->loadHelpers($user->_id, $compte_rendu->object_class);
$templateManager->loadLists($user->_id);
$templateManager->applyTemplate($compte_rendu);

$where = array();
$where[] = "(
  chir_id = '$user->_id' OR 
  function_id = '$user->function_id' OR 
  group_id = '{$user->_ref_function->group_id}'
)";
$order = "chir_id, function_id, group_id";
$chirLists = new CListeChoix;
$chirLists = $chirLists->loadList($where, $order);
$lists = $templateManager->getUsedLists($chirLists);

// Récupération des éléments de destinataires de courrier
$isCourrier = $templateManager->isCourrier();
$destinataires = array();
if($isCourrier) {
  CDestinataire::makeAllFor($object);
  $destinataires = CDestinataire::$destByClass;
}

$templateManager->initHTMLArea();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("templateManager", $templateManager);
$smarty->assign("compte_rendu"   , $compte_rendu);
$smarty->assign("modele_id"      , $modele_id);
$smarty->assign("lists"          , $lists);
$smarty->assign("destinataires"  , $destinataires);
$smarty->assign("user_id"        , $user->_id);
$smarty->display("edit_compte_rendu.tpl");

?>