<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsEdit();

$compte_rendu_id = mbGetValueFromGet("compte_rendu_id"   , 0);
$modele_id       = mbGetValueFromGet("modele_id"         , 0);
$praticien_id    = mbGetValueFromGet("praticien_id"      , 0);
$type            = mbGetValueFromGet("type"              , 0);
$pack_id         = mbGetValueFromGet("pack_id"           , 0);
$object_id       = mbGetValueFromGet("object_id"         , 0);
$target_id       = mbGetValueFromGet("target_id");
$target_class    = mbGetValueFromGet("target_class");

// Faire ici le test des différentes variables dont on a besoin

$compte_rendu = new CCompteRendu;

// Modification d'un document
if ($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
}

// Création à partir d'un modèle
else {
	$header = null;
	$footer = null;
	
  $compte_rendu->load($modele_id);
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
    $compte_rendu->source = $pack->_source;
    
    // Parcours des modeles du pack pour trouver le premier header et footer
    foreach($pack->_modeles as $mod) {
    	if ($mod->header_id || $mod->footer_id) {
    		$mod->loadComponents();
    	}
    	if (!isset($header)) $header = $mod->_ref_header;
    	if (!isset($footer)) $footer = $mod->_ref_footer;
    	if ($header && $footer) break;
    }
  }
  
  if ($header || $footer) {
  	$header->height = isset($header->height) ? $header->height : 20;
  	$footer->height = isset($footer->height) ? $footer->height : 20;
  	
    $style = "
      <style type='text/css'>
      #header {
        height: {$header->height}px;
      }

      #footer {
        height: {$footer->height}px;
      }";
    
    if ($header->_id) {
      $header->source = "<div id='header'>$header->source</div>";
      $header->height += 20;
      $compte_rendu->header_id = null;
    }
    
    if ($footer->_id) {
      $footer->source = "<div id='footer'>$footer->source</div>";
      $footer->height += 20;
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
      }
      </style>";

    $compte_rendu->source = "<div id='body'>$compte_rendu->source</div>";
    $compte_rendu->source = $style . $header->source . $footer->source . $compte_rendu->source;
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
$templateManager->document = $compte_rendu->source;
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
$smarty->assign("lists"          , $lists);
$smarty->assign("destinataires"  , $destinataires);

$smarty->display("edit_compte_rendu.tpl");

?>