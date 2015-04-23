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

CCanDo::checkRead();

$group = CGroups::loadCurrent();

// Récupération des paramètres
$typeVue = CValue::getOrSession("typeVue");
$selPrat = CValue::getOrSession("selPrat");
$services_ids = CValue::getOrSession("services_ids");
$group_id     = CValue::get("g");

$date_recherche = CValue::getOrSession("date_recherche", CMbDT::dateTime());

// Détection du changement d'établissement
$services_ids = CService::getServicesIdsPref($services_ids);

// Liste des chirurgiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Liste des services
$services = new CService();
$where = array();
$where["group_id"]  = "= '$group->_id'";
$where["cancelled"] = "= '0'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ, $where, $order);

$listAff = null;
$libre = null;
$autre_sexe_chambre = array();

$ds    = CSQLDataSource::get("std");

//
// Cas de l'affichage des lits libres
//
if ($typeVue == 0) {

  // Recherche de tous les lits disponibles
  $sql = "SELECT lit.lit_id
          FROM affectation
          LEFT JOIN lit ON lit.lit_id = affectation.lit_id
          LEFT JOIN chambre ON lit.chambre_id = chambre.chambre_id
          WHERE '$date_recherche' BETWEEN affectation.entree AND affectation.sortie
          AND chambre.annule = '0'
          AND lit.annule = '0'
          AND affectation.effectue = '0'
          GROUP BY lit.lit_id";

  $occupes = $ds->loadlist($sql);
  $arrayIn = array();
  foreach ($occupes as $key => $occupe) {
    $arrayIn[] = $occupe["lit_id"];
  }
  $notIn = count($arrayIn) > 0 ? implode(', ', $arrayIn) : 0;
  $libre = array();

  if (is_array($services_ids) && count($services_ids)) {
    $sql = "SELECT lit.chambre_id, lit.lit_id, lit.nom AS lit, chambre.nom AS chambre, chambre.caracteristiques as caracteristiques, service.nom AS service, MIN(affectation.entree) AS limite
            FROM lit
            LEFT JOIN affectation ON affectation.lit_id = lit.lit_id
            AND (affectation.entree > '$date_recherche' OR affectation.entree IS NULL)
            LEFT JOIN chambre ON chambre.chambre_id = lit.chambre_id
            LEFT JOIN service ON service.service_id = chambre.service_id
            WHERE lit.lit_id NOT IN($notIn)
            AND chambre.annule = '0'
            AND lit.annule = '0'
            AND service.group_id = '$group->_id'
            AND service.service_id ".CSQLDataSource::prepareIn($services_ids)."
            GROUP BY lit.lit_id
            ORDER BY service.nom, chambre.nom, lit.nom, limite DESC";
    $libre = $ds->loadList($sql);

    $sql = "SELECT lit.chambre_id, patients.sexe, lit.nom AS lit, chambre.nom AS chambre, service.nom AS service
            FROM affectation
            LEFT JOIN lit ON lit.lit_id = affectation.lit_id
            LEFT JOIN chambre ON chambre.chambre_id = lit.chambre_id
            LEFT JOIN service ON chambre.service_id = chambre.service_id
            LEFT JOIN sejour ON sejour.sejour_id = affectation.sejour_id
            LEFT JOIN patients ON patients.patient_id = sejour.patient_id
            WHERE '$date_recherche' BETWEEN affectation.entree AND affectation.sortie
            AND affectation.lit_id IS NOT NULL
            AND lit.chambre_id ".CSQLDataSource::prepareIn(CMbArray::pluck($libre, "chambre_id")).
           " AND lit.lit_id ".CSQLDataSource::prepareNotIn(CMbArray::pluck($libre, "lit_id")).
           " GROUP BY lit.lit_id";
    $autre_sexe_chambre = $ds->loadList($sql);
    foreach ($autre_sexe_chambre as $key=>$_autre) {
      $autre_sexe_chambre[$_autre["chambre_id"]] = $_autre;
    }
  }
}

//
// Cas de l'affichage des lits d'un praticien
//
else if ($typeVue == 1) {
  // Recherche des patients du praticien
  // Qui ont une affectation
  $listAff = array(
    "Aff"    => array(),
    "NotAff" => array()
  );

  if (is_array($services_ids) && count($services_ids)) {

    $affectation = new CAffectation;
    $ljoin = array(
      "lit"     => "affectation.lit_id = lit.lit_id",
      "chambre" => "chambre.chambre_id = lit.chambre_id",
      "service" => "service.service_id = chambre.service_id",
      "sejour"  => "sejour.sejour_id   = affectation.sejour_id"
    );
    $where = array(
      "affectation.entree"  => "< '$date_recherche'",
      "affectation.sortie"  => "> '$date_recherche'",
      "service.service_id"  => CSQLDataSource::prepareIn($services_ids),
      "sejour.praticien_id" => CSQLDataSource::prepareIn(array_keys($listPrat), $selPrat),
      "sejour.group_id"     => "= '$group->_id'"
    );
    $order = "service.nom, chambre.nom, lit.nom";
    $listAff["Aff"] = $affectation->loadList($where, $order, null, null, $ljoin);
    foreach ($listAff["Aff"] as $_aff) {
      /** @var CAffectation $_aff */
      $_aff->loadView();
      $_aff->loadRefSejour();
      $_aff->_ref_sejour->loadRefPatient();
      $_aff->_ref_sejour->_ref_praticien = $listPrat[$_aff->_ref_sejour->praticien_id];

      $_aff->loadRefLit();
      $_aff->_ref_lit->loadCompleteView();
      foreach ($_aff->_ref_sejour->_ref_operations as $_operation) {
        $_operation->loadExtCodesCCAM();
      }
    }
  }
  else {
    // Qui n'ont pas d'affectation
    $sejour = new CSejour();
    $where = array(
      "sejour.entree"  => "< '$date_recherche'",
      "sejour.sortie"  => "> '$date_recherche'",
      "sejour.praticien_id" => CSQLDataSource::prepareIn(array_keys($listPrat), $selPrat),
      "sejour.group_id"     => "= '$group->_id'"
    );
    $order = "sejour.entree, sejour.sortie, sejour.praticien_id";
    $listAff["NotAff"] = $sejour->loadList($where, $order);
    foreach ($listAff["NotAff"] as $_sejour) {
      /** @var CSejour $_sejour */
      $_sejour->loadRefPatient();
      $_sejour->_ref_praticien = $listPrat[$_sejour->praticien_id];
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date_recherche", $date_recherche);
$smarty->assign("libre"         , $libre);
$smarty->assign("typeVue"       , $typeVue);
$smarty->assign("selPrat"       , $selPrat);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("listAff"       , $listAff);
$smarty->assign("autre_sexe_chambre", $autre_sexe_chambre);
$smarty->assign("canPlanningOp" , CModule::getCanDo("dPplanningOp"));

$smarty->display("vw_recherche.tpl");
