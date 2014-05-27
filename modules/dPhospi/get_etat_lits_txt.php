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

/*
 * Pour acceder à cette page ==>
 * http://localhost/mediboard/index.php?m=dPhospi&a=get_etat_lits_txt&dialog=1&suppressHeaders=1&login=user:password;
 * renvoi :
 * NOM;Prénom;patient_id;service_id;chambre_id;lit_id;sexe(m ou f);naissance(YYYYMMJJ);
 * entree(YYYYMMDD);entree(HHMM);sortie(YYYYMMDD);sortie(HHMM);type_hospi(comp ou ambu)
*/

// Date actuelle
$date = CValue::get("date", CMbDT::dateTime());

// Affectation a la date $date
$affectation = new CAffectation();

$ljoinAffect = array();
$ljoinAffect["sejour"] = "sejour.sejour_id = affectation.sejour_id";

$whereAffect = array();
$whereAffect["affectation.entree"]    = "<= '$date'";
$whereAffect["affectation.sortie"]    = ">= '$date'";
$whereAffect["affectation.sejour_id"] = "!= '0'";
$whereAffect["sejour.group_id"]       = "= '".CGroups::loadCurrent()->_id."'";
$whereAffect["sejour.annule"]         = "= '0'";

$groupAffect = "sejour_id";

/** @var CAffectation[] $affectations */
$affectations = $affectation->loadList($whereAffect, null, null, $groupAffect, $ljoinAffect);

// Chargements de masse
$lits       = CMbObject::massLoadFwdRef($affectations, "lit_id");
$sejours    = CMbObject::massLoadFwdRef($affectations, "sejour_id");

$chambres   = CMbObject::massLoadFwdRef($lits    , "chambre_id");
$services   = CMbObject::massLoadFwdRef($chambres, "service_id");

$praticiens = CMbObject::massLoadFwdRef($sejours , "praticien_id");
$patients   = CMbObject::massLoadFwdRef($sejours , "patient_id");

$list_affectations = array();

foreach ($affectations as $key => $_affectation) {
  $_affectation->loadRefLit();
  $_affectation->_ref_lit->loadRefChambre();
  $_affectation->_ref_lit->_ref_chambre->loadRefService();
  $_affectation->loadRefSejour();
  $_affectation->_ref_sejour->loadRefPraticien();
  $_affectation->_ref_sejour->loadRefPatient();

  $list_affectations[$key]["nom"]          = $_affectation->_ref_sejour->_ref_patient->nom;
  $list_affectations[$key]["prenom"]       = $_affectation->_ref_sejour->_ref_patient->prenom;
  $list_affectations[$key]["id"]           = $_affectation->_ref_sejour->_ref_patient->_id;
  $list_affectations[$key]["service"]      = $_affectation->_ref_lit->_ref_chambre->_ref_service->_id;
  $list_affectations[$key]["chambre"]      = $_affectation->_ref_lit->_ref_chambre->_id;
  $list_affectations[$key]["lit"]          = $_affectation->_ref_lit->_id;
  $list_affectations[$key]["sexe"]         = $_affectation->_ref_sejour->_ref_patient->sexe;
  $list_affectations[$key]["naissance"]    = CMbDT::format($_affectation->_ref_sejour->_ref_patient->naissance, "%Y%m%d");
  $list_affectations[$key]["date_entree"]  = CMbDT::format(CMbDT::date($_affectation->_ref_sejour->entree), "%Y%m%d");
  $list_affectations[$key]["heure_entree"] = CMbDT::format(CMbDT::time($_affectation->_ref_sejour->entree), "%H%M");
  $list_affectations[$key]["date_sortie"]  = CMbDT::format(CMbDT::date($_affectation->_ref_sejour->sortie), "%Y%m%d");
  $list_affectations[$key]["heure_sortie"] = CMbDT::format(CMbDT::time($_affectation->_ref_sejour->sortie), "%H%M");
  $list_affectations[$key]["type"]         = $_affectation->_ref_sejour->type;
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_affectations", $list_affectations);

$smarty->display("get_etat_lits_txt.tpl");
