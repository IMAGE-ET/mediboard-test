<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkAdmin();

$secteur_id    = CValue::getOrSession("secteur_id");
$service_id    = CValue::getOrSession("service_id");
$chambre_id    = CValue::getOrSession("chambre_id");
$lit_id        = CValue::getOrSession("lit_id");
$uf_id         = CValue::getOrSession("uf_id");
$prestation_id = CValue::getOrSession("prestation_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Chargement du secteur à ajouter / éditer
$secteur = new CSecteur;
$secteur->group_id = $group->_id;
$secteur->load($secteur_id);
$secteur->loadRefsNotes();
$secteur->loadRefsServices();

// Chargement du service à ajouter / éditer
$service = new CService();
$service->group_id = $group->_id;
$service->load($service_id);
$service->loadRefsNotes();

// Récupération de la chambre à ajouter / éditer
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

// Chargement du lit à ajouter / éditer
$lit = new CLit();
$lit->load($lit_id);
$lit->loadRefChambre();

// Récupération des chambres/services/secteurs
$where = array();
$where["group_id"] = "= '$group->_id'";
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ, $where, $order);
foreach ($services as $_service) {
  foreach ($_service->loadRefsChambres() as $_chambre) {
    $_chambre->loadRefs();
  }
}

$secteurs = $secteur->loadListWithPerms(PERM_READ, $where, $order);

// Chargement de l'uf à ajouter/éditer
$uf = new CUniteFonctionnelle();
$uf->group_id = $group->_id;
$uf->load($uf_id);
$uf->loadRefsNotes();

// Récupération des ufs
$order = "group_id, code";
$ufs = array(
  "hebergement" => $uf->loadGroupList(array("type" => "= 'hebergement'"), $order),
  "medicale"    => $uf->loadGroupList(array("type" => "= 'medicale'"), $order),
  "soins"       => $uf->loadGroupList(array("type" => "= 'soins'"), $order),
);

// Chargement de la prestation à ajouter/éditer
$prestation = new CPrestation();
$prestation->group_id = $group->_id;
$prestation->load($prestation_id);
$prestation->loadRefsNotes();

// Récupération des prestations
$presta = new CPrestation;
$presta->group_id = $group->_id;
$prestations = $presta->loadMatchingList("nom");

$praticiens = CAppUI::$user->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("services"      , $services);
$smarty->assign("service"       , $service);
$smarty->assign("tag_service"   , CService::getTagService($group->_id));
$smarty->assign("secteurs"      , $secteurs);
$smarty->assign("secteur"       , $secteur);
$smarty->assign("chambre"       , $chambre);
$smarty->assign("tag_chambre"   , CChambre::getTagChambre($group->_id));
$smarty->assign("lit"           , $lit);
$smarty->assign("tag_lit"       , CLit::getTagLit($group->_id));
$smarty->assign("ufs"           , $ufs);
$smarty->assign("uf"            , $uf);
$smarty->assign("prestations"   , $prestations);
$smarty->assign("prestation"    , $prestation);
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

$smarty->display("vw_idx_infrastructure.tpl");