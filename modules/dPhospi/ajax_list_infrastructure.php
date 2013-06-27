<?php /* $Id: ajax_list_infrastructure.php 13247 2011-09-23 08:43:46Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 13247 $
* @author 
*/

CCanDo::checkAdmin();

$type_name    = CValue::get("type_name");

$service_id    = CValue::getOrSession("service_id");
$chambre_id    = CValue::getOrSession("chambre_id");
$lit_id        = CValue::getOrSession("lit_id");
$uf_id         = CValue::getOrSession("uf_id");
$prestation_id = CValue::getOrSession("prestation_id");
$secteur_id    = CValue::getOrSession("secteur_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

$praticiens = CAppUI::$user->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

// Récupération des chambres/services
$where = array();
$where["group_id"] = "= '$group->_id'";

if ($type_name == "services") {
  // Chargement du service à ajouter/editer
  $service = new CService();
  $service->group_id = $group->_id;
  $service->load($service_id);
  $service->loadRefsNotes();
  $services = $service->loadListWithPerms(PERM_READ, $where, "nom");

  foreach ($services as $_service) {
    foreach ($_service->loadRefsChambres() as $_chambre) {
      $_chambre->loadRefs();
    }
  }

  $smarty->assign("services"    , $services);
  $smarty->assign("service"     , $service);
  $smarty->assign("tag_service" , CService::getTagService($group->_id));
  $smarty->display("inc_vw_idx_services.tpl");
}

if ($type_name == "chambres") {
  // Récupération de la chambre à ajouter/editer
  $chambre = new CChambre();
  $chambre->load($chambre_id);
  $chambre->loadRefsNotes();
  $chambre->loadRefService();
  foreach ($chambre->loadRefsLits() as $_lit) {
    $_lit->loadRefsNotes();
  }
  
  if (!$chambre->_id) {
    CValue::setSession("lit_id", 0);
  }

  // Chargement du lit à ajouter/editer
  $lit = new CLit();
  $lit->load($lit_id);
  $lit->loadRefChambre();

  $service = new CService();
  $services = $service->loadListWithPerms(PERM_READ, $where, "nom");

  foreach ($services as $_service) {
    foreach ($_service->loadRefsChambres() as $_chambre) {
      $_chambre->loadRefs();
    }
  }

  $smarty->assign("services"    , $services);
  $smarty->assign("chambre"     , $chambre);
  $smarty->assign("tag_chambre" , CChambre::getTagChambre($group->_id));
  $smarty->assign("lit"         , $lit);
  $smarty->assign("tag_lit"     , CLit::getTagLit($group->_id));
  $smarty->display("inc_vw_idx_chambres.tpl");
}

if ($type_name == "UF") {
  // Chargement de l'uf à ajouter/éditer
  $uf = new CUniteFonctionnelle();
  $uf->group_id = $group->_id;
  $uf->load($uf_id);
  $uf->loadRefsNotes();
  
  // Récupération des ufs
  $order = "group_id, code";
  $ufs = array("hebergement" => $uf->loadGroupList(array("type" => "= 'hebergement'"), $order),
               "medicale"    => $uf->loadGroupList(array("type" => "= 'medicale'"), $order),
               "soins"       => $uf->loadGroupList(array("type" => "= 'soins'"), $order));


  $smarty->assign("ufs", $ufs);
  $smarty->assign("uf", $uf);
  $smarty->display("inc_vw_idx_ufs.tpl");
}

if ($type_name == "prestations") {
  // Chargement de la prestation à ajouter/éditer
  $prestation = new CPrestation();
  $prestation->group_id = $group->_id;
  $prestation->load($prestation_id);
  $prestation->loadRefsNotes();
  
  // Récupération des prestations
  $order = "group_id, nom";
  $prestations = $prestation->loadList(null, $order);

  foreach ($prestations as $_prestation) {
    $_prestation->loadRefGroup();
  }

  $smarty->assign("prestation"  , $prestation);
  $smarty->assign("prestations" , $prestations);
  $smarty->display("inc_vw_idx_prestations.tpl");
}

if ($type_name == "secteurs") {
   // Chargement du secteur à ajouter / éditer
  $secteur = new CSecteur;
  $secteur->group_id = $group->_id;
  $secteur->load($secteur_id);
  $secteur->loadRefsNotes();
  $secteur->loadRefsServices();

  // Récupération des prestations
  $order = "group_id, nom";

  // Récupération des secteurs
  $secteurs = $secteur->loadListWithPerms(PERM_READ, $where, $order);

  $smarty->assign("secteurs", $secteurs);
  $smarty->assign("secteur", $secteur);
  $smarty->display("inc_vw_idx_secteurs.tpl");
}