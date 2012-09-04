<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

$compte_rendu_id = CValue::get("compte_rendu_id"   , 0);
$modele_id       = CValue::get("modele_id"         , null);
$praticien_id    = CValue::get("praticien_id"      , 0);
$type            = CValue::get("type"              , 0);
$pack_id         = CValue::get("pack_id"           , 0);
$object_id       = CValue::get("object_id"         , 0);
$switch_mode     = CValue::get("switch_mode"       , 0);
$target_id       = CValue::get("target_id");
$target_class    = CValue::get("target_class");

// Faire ici le test des différentes variables dont on a besoin
$compte_rendu = new CCompteRendu;

// Modification d'un document
if ($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
  $compte_rendu->loadContent();
  $compte_rendu->loadFile();
}
// Création à partir d'un modèle vide
else if ($modele_id == 0 && !$pack_id) {
  $compte_rendu->valueDefaults();
  $compte_rendu->author_id = CAppUI::$user->_id;
  $compte_rendu->object_id = $object_id;
  $compte_rendu->object_class = $target_class;
  $compte_rendu->_ref_object = new $target_class;
  $compte_rendu->_ref_object->load($object_id);
  $compte_rendu->updateFormFields();
}
// Création à partir d'un modèle
else {
  $compte_rendu->load($modele_id);
  $compte_rendu->loadFile();
  $compte_rendu->loadContent();
  $compte_rendu->_id = null;
  $compte_rendu->function_id = null;
  $compte_rendu->group_id = null;
  $compte_rendu->object_id = $object_id;
  $compte_rendu->_ref_object = null;
  $compte_rendu->modele_id = $modele_id;
  $compte_rendu->author_id = CAppUI::$user->_id;
  $header_id = null;
  $footer_id = null;
  
  // Utilisation des headers/footers
  if ($compte_rendu->header_id || $compte_rendu->footer_id) {
    $header_id = $compte_rendu->header_id;
    $footer_id = $compte_rendu->footer_id;
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
    
    $pack->loadContent();
    $compte_rendu->nom = $pack->nom;
    $compte_rendu->object_class = $pack->object_class;
    $compte_rendu->fast_edit = $pack->fast_edit;
    $compte_rendu->fast_edit_pdf = $pack->fast_edit_pdf;
    $compte_rendu->_source = $pack->_source;
    $compte_rendu->modele_id = null;
    
    $pack->loadHeaderFooter();
    
    $header_id = $pack->_header_found->_id;
    $footer_id = $pack->_footer_found->_id;
    
    // Marges et format
    $first_modele = reset($pack->_back['modele_links']);
    $first_modele = $first_modele->_ref_modele;
    $compte_rendu->margin_top    = $first_modele->margin_top;
    $compte_rendu->margin_left   = $first_modele->margin_left;
    $compte_rendu->margin_right  = $first_modele->margin_right;
    $compte_rendu->margin_bottom = $first_modele->margin_bottom;
    $compte_rendu->page_height   = $first_modele->page_height;
    $compte_rendu->page_width    = $first_modele->page_width;
    $compte_rendu->font          = $first_modele->font;
    $compte_rendu->size          = $first_modele->size;
  }
  $compte_rendu->_source = $compte_rendu->generateDocFromModel(null, $header_id, $footer_id);
  $compte_rendu->updateFormFields();
}

$compte_rendu->loadRefsFwd();

$compte_rendu->_ref_object->loadRefsFwd();
$object =& $compte_rendu->_ref_object;  

// Calcul du user concerné
$user = new CMediusers();
$user->load(CAppUI::$user->_id);

// Chargement dans l'ordre suivant pour les listes de choix si null :
// - user courant
// - anesthésiste
// - praticien de la consultation

if (!$user->isPraticien()) {
  if ($object instanceof CConsultAnesth) {
    $operation = $object->loadRefOperation();
    $anesth = $operation->_ref_anesth;
    $user->_id = null;
    if ($operation->_id && $anesth->_id) {
      $user->_id = $anesth->_id;
    }
    
    if ($user->_id == null)
      $user->_id = $object->_ref_consultation->_praticien_id;
  }
  if ($object instanceof CCodable) {
    $user->_id = $object->_praticien_id;
  }
}

$user->load($user->_id);
$user->loadRefFunction();

// Chargement des catégories
$listCategory = CFilesCategory::listCatClass($compte_rendu->object_class);

// Décompte des imprimantes disponibles pour l'impression serveur
$user_printers = CMediusers::get();
$function      = $user_printers->loadRefFunction();
$nb_printers   = $function->countBackRefs("printers");

// Gestion du template
$templateManager = new CTemplateManager($_GET);
$templateManager->isModele = false;
$object->fillTemplate($templateManager);
$templateManager->document = $compte_rendu->_source;
$templateManager->loadHelpers($user->_id, $compte_rendu->object_class);
$templateManager->loadLists($user->_id);
$templateManager->applyTemplate($compte_rendu);

$where = array();
$where[] = "(
  user_id = '$user->_id' OR 
  function_id = '$user->function_id' OR 
  group_id = '{$user->_ref_function->group_id}'
)";
$order = "user_id, function_id, group_id";
$userLists = new CListeChoix;
$userLists = $userLists->loadList($where, $order);
$lists = $templateManager->getUsedLists($userLists);

// Afficher le bouton correpondant si on détecte un élément de publipostage
$isCourrier = $templateManager->isCourrier();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCategory"  , $listCategory);
$smarty->assign("compte_rendu"  , $compte_rendu);
$smarty->assign("modele_id"     , $modele_id);
$smarty->assign("lists"         , $lists);
$smarty->assign("isCourrier"    , $isCourrier);
$smarty->assign("user_id"       , $user->_id);
$smarty->assign("user_view"     , $user->_view);
$smarty->assign("object_id"     , $object_id);
$smarty->assign('object_class'  , CValue::get("object_class", $compte_rendu->object_class));
$smarty->assign("nb_printers"   , $nb_printers);
$smarty->assign("pack_id"       , $pack_id);

preg_match_all("/(:?\[\[Texte libre - ([^\]]*)\]\])/i",$compte_rendu->_source, $matches);

$templateManager->textes_libres = $matches[2];

// Suppression des doublons
$templateManager->textes_libres = array_unique($templateManager->textes_libres);

if (isset($compte_rendu->_ref_file->_id)) {
  $smarty->assign("file", $compte_rendu->_ref_file);
}

$smarty->assign("textes_libres", $templateManager->textes_libres);

$exchange_source = CExchangeSource::get("mediuser-".CAppUI::$user->_id);
$smarty->assign("exchange_source", $exchange_source);

// Ajout d'entête / pied de page à la volée
$headers = array();
$footers = array();

if (CAppUI::conf("dPcompteRendu CCompteRendu header_footer_fly") && $modele_id) {
  if (!$compte_rendu->header_id) {
    $headers = CCompteRendu::loadAllModelesFor(CAppUI::$user->_id, "prat", $compte_rendu->object_class, "header");
  }
  if (!$compte_rendu->footer_id) {
    $footers = CCompteRendu::loadAllModelesFor(CAppUI::$user->_id, "prat", $compte_rendu->object_class, "footer");
  }
}

$smarty->assign("headers", $headers);
$smarty->assign("footers", $footers);

// Nettoyage des balises meta et link.
// Pose problème lors de la présence d'un entête et ou/pied de page
$source = &$templateManager->document;

$source = preg_replace("/<meta\s*[^>]*\s*[^\/]>/", '', $source);
$source = preg_replace("/(<\/meta>)+/i", '', $source);
$source = preg_replace("/<link\s*[^>]*\s*>/", '', $source);

$pdf_thumbnails = CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails");
$pdf_and_thumbs = CAppUI::pref("pdf_and_thumbs");

if (CValue::get("reloadzones") == 1) {
  $smarty->display("inc_zones_fields.tpl");
}
else if (!$compte_rendu_id && !$switch_mode && ($compte_rendu->fast_edit || ($compte_rendu->fast_edit_pdf && $pdf_thumbnails && $pdf_and_thumbs))) {
  $printers = $function->loadBackRefs("printers");
  
  if (is_array($printers)) {
    foreach($printers as $_printer) {
        $_printer->loadTargetObject();
    }
  }
  
  $smarty->assign("_source"     , $templateManager->document);
  $smarty->assign("printers"    , $printers);
  $smarty->assign("object_guid" , CValue::get("object_guid"));
  $smarty->assign("unique_id"   , CValue::get("unique_id"));
  
  $smarty->display("fast_mode.tpl");
}
else { 
  // Charger le document précédent et suivant
  $prevnext = array();
  if ($compte_rendu->_id) {
    $object = new $compte_rendu->object_class;
    $object->load($compte_rendu->object_id);
    $object->loadRefsDocs();
    
    $prevnext = CMbArray::getPrevNextKeys($object->_ref_documents, $compte_rendu->_id);
  }
  
  $templateManager->initHTMLArea();
  $smarty->assign("switch_mode"    , CValue::get("switch_mode", 0));
  $smarty->assign("templateManager", $templateManager);
  $smarty->assign("prevnext", $prevnext);
  $smarty->display("edit_compte_rendu.tpl");
}
?>