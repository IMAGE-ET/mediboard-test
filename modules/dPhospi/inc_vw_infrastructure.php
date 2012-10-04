<?php /* $Id: inc_vw_infrastructure.php 13247 2011-09-23 08:43:46Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 13247 $
* @author 
*/

CCanDo::checkAdmin();

$use_service    = CValue::get("service_id");
$service_id     = CValue::getOrSession("service_id");
$use_chambre    = CValue::get("chambre_id");
$chambre_id     = CValue::getOrSession("chambre_id");
$lit_id         = CValue::getOrSession("lit_id");
$use_uf         = CValue::get("uf_id");
$uf_id          = CValue::getOrSession("uf_id");
$use_prestation = CValue::get("prestation_id");
$prestation_id  = CValue::getOrSession("prestation_id");
$use_secteur    = CValue::get("secteur_id");
$secteur_id     = CValue::getOrSession("secteur_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

if ($use_service != null) {
  // Chargement du service à ajouter/editer
  $service = new CService();
  $service->group_id = $group->_id;
  $service->load($service_id);
  $service->loadRefsNotes();
}

if ($use_chambre != null) {
  // Récupération de la chambre à ajouter/editer
  $chambre = new CChambre();
  $chambre->load($chambre_id);
  $chambre->loadRefsNotes();
  $chambre->loadRefService();
  foreach ($chambre->loadRefsLits(true) as $_lit) {
    $_lit->loadRefsNotes();
  }
  
  if (!$chambre->_id) {
    CValue::setSession("lit_id", 0);
  }

  // Chargement du lit à ajouter/editer
  $lit = new CLit();
  $lit->load($lit_id);
  $lit->loadRefChambre();
  
  // Récupération des chambres/services
  $where = array();
  $where["group_id"] = "= '$group->_id'";
  $order = "nom";
  
  $service = new CService();
  $services = $service->loadListWithPerms(PERM_READ,$where, $order);
  foreach ($services as $_service) {
    foreach ($_service->loadRefsChambres() as $_chambre) {
      $_chambre->loadRefs();
    }
  }
}

if ($use_uf != null) {
  // Chargement de l'uf à ajouter/éditer
  $uf = new CUniteFonctionnelle();
  $uf->group_id = $group->_id;
  $uf->load($uf_id);
  $uf->loadRefsNotes();
  
  // Récupération des ufs
  $order = "group_id, code";
  $ufs = $uf->loadList(null, $order);
}

if ($use_prestation != null) {
  // Chargement de la prestation à ajouter/éditer
  $prestation = new CPrestation();
  $prestation->group_id = $group->_id;
  $prestation->load($prestation_id);
  $prestation->loadRefsNotes();
  
  // Récupération des prestations
  $order = "group_id, nom";
  $prestations = $prestation->loadList(null, $order);
  foreach ($prestations as $_prestation){
    $_prestation->loadRefGroup();
  }
}

if ($use_secteur != null) {
  $secteur = new CSecteur;
  $secteur->group_id = $group->_id;
  $secteur->load($secteur_id);
  $secteur->loadRefsNotes();
  $secteur->loadRefsServices();
}

$praticiens = CAppUI::$user->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

if ($use_service != null) {
  $smarty->assign("service"       , $service);
  $smarty->display("inc_vw_service.tpl");
}
elseif ($use_chambre != null) {
  $smarty->assign("services"       , $services);
  $smarty->assign("chambre"       , $chambre);
  $smarty->assign("lit"           , $lit);
  $smarty->display("inc_vw_chambre.tpl");
}
elseif ($use_uf != null) {
  $smarty->assign("uf"            , $uf);
  $smarty->display("inc_vw_uf.tpl");
}
elseif ($use_prestation != null) {
  $smarty->assign("prestation"    , $prestation);
  $smarty->display("inc_vw_prestation.tpl");
}
elseif ($use_secteur != null) {
  $smarty->assign("secteur", $secteur);
  $smarty->display("inc_vw_secteur.tpl");
}
?>