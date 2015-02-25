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

// Récupération des chambres/services/secteurs
$where = array();
$where["group_id"] = "= '$group->_id'";
$order = "nom";

/** @var CService[] $services */
$service= new CService();
$services = $service->loadListWithPerms(PERM_READ, $where, $order);
foreach ($services as $_service) {
  foreach ($_service->loadRefsChambres() as $_chambre) {
    $_chambre->loadRefsLits(true);
  }
}

$secteurs = $secteur->loadListWithPerms(PERM_READ, $where, $order);

// Chargement de l'uf à ajouter/éditer
$uf = new CUniteFonctionnelle();
$uf->group_id = $group->_id;
$uf->load($uf_id);
$uf->loadRefUm();
$uf->loadRefsNotes();

// Récupération des ufs
$order = "group_id, code";
$ufs = array(
  "hebergement" => $uf->loadGroupList(array("type" => "= 'hebergement'"), $order),
  "medicale"    => $uf->loadGroupList(array("type" => "= 'medicale'"), $order),
  "soins"       => $uf->loadGroupList(array("type" => "= 'soins'"), $order),
);

// Récupération des Unités Médicales (pmsi)
$ums = array ();
$ums_infos = array ();
$um  = new CUniteMedicale();

if (CSQLDataSource::get("sae") && CModule::getActive("atih")) {
  $um_infos  = new CUniteMedicaleInfos();
  $ums = $um->loadListUm();
  $group = CGroups::loadCurrent();
  $where["group_id"] = " = '$group->_id'";
  $where["mode_hospi"] = " IS NOT NULL";
  $where["nb_lits"] = " IS NOT NULL";
  $ums_infos = $um_infos->loadList($where);
}

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
$smarty->assign("secteurs"      , $secteurs);
$smarty->assign("secteur"       , $secteur);
$smarty->assign("ufs"           , $ufs);
$smarty->assign("uf"            , $uf);
$smarty->assign("ums"           , $ums);
$smarty->assign("ums_infos"     , $ums_infos);
$smarty->assign("prestations"   , $prestations);
$smarty->assign("prestation"    , $prestation);
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

$smarty->display("vw_idx_infrastructure.tpl");