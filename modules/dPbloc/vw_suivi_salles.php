<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

/** @var CBlocOperatoire[] $listBlocs */
$listBlocs  = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$date_suivi = CAppUI::pref("suivisalleAutonome") ? CValue::get("date", CMbDT::date()) : CValue::getOrSession("date", CMbDT::date());
$bloc_id    = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);
if (!key_exists($bloc_id, $listBlocs)) {
  $bloc_id = reset($listBlocs)->_id;
}
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();

// Chargement de la liste des salles de chaque bloc
foreach ($listBlocs as $_bloc) {
  $_bloc->loadRefsSalles();
}

// Chargement des Anesthésistes
$listAnesths = new CMediusers();
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Chargement des Chirurgiens
$listChirs = new CMediusers();
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$salle = new CSalle();
$where = array("bloc_id" => "='$bloc->_id'");
$bloc->_ref_salles = $salle->loadListWithPerms(PERM_READ, $where, "nom");

$systeme_materiel_expert = CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert";

foreach ($bloc->_ref_salles as &$salle) {
  /** @var CSalle $salle */
  $salle->loadRefsForDay($date_suivi);
  if ($systeme_materiel_expert) {
    foreach ($salle->_ref_urgences as $_operation) {
      $besoins = $_operation->loadRefsBesoins();
      CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
      foreach ($besoins as $_besoin) {
        $_besoin->loadRefTypeRessource();
      }
    }
  }
}

// Interventions hors plages non traitées
$op = new COperation();
$where = array();
$ljoin = array();
$ljoin["sejour"] = "operations.sejour_id = sejour.sejour_id";
$where["operations.date"] = "= '$date_suivi'";
$where["operations.salle_id"] = "IS NULL";
$where["operations.plageop_id"] = "IS NULL";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

/** @var COperation[] $non_traitees */
$non_traitees = $op->loadList($where, null, null, null, $ljoin);

foreach ($non_traitees as $_operation) {
  $_operation->loadRefChir();
  $_operation->loadRefPatient();
  $_operation->loadExtCodesCCAM();
  $_operation->loadRefPlageOp();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"     , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listBlocs"      , $listBlocs);
$smarty->assign("bloc"           , $bloc);
$smarty->assign("date_suivi"     , $date_suivi);
$smarty->assign("operation_id"   , 0);
$smarty->assign("non_traitees"   , $non_traitees);

$smarty->display("vw_suivi_salles.tpl");
