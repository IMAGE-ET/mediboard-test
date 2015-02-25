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
 * http://localhost/mediboard/index.php?m=dPhospi&raw=get_etat_lits_txt&login=user:password;
 * renvoi :
 * NOM;Prénom;patient_id;service_id;chambre_id;lit_id;sexe(m ou f);naissance(YYYYMMJJ);
 * entree(YYYYMMDD);entree(HHMM);sortie(YYYYMMDD);sortie(HHMM);type_hospi(comp ou ambu)
*/

/*
 * Ajout du paramètre detail_lit pour avoir :
 * NOM;Prénom;patient_id;NOM_NAISSANCE,service_id;chambre_id;LIT_NOM;lit_id;sexe;naissance;entree;entree;sortie;sortie;type_hospi
 */

/*
 * Ajout du paramètre IPP pour avoir :
 * NOM;Prénom;patient_id;NOM_NAISSANCE,service_id;chambre_id;LIT_NOM;lit_id;sexe;naissance;entree;entree;sortie;sortie;type_hospi
 */

// Date actuelle
$date       = CValue::get("date", CMbDT::dateTime());
$detail_lit = CValue::get("detail_lit", 0);
$with_ambu  = CValue::get("with_ambu" , 1);
$IPP        = CValue::get("IPP"       , 0);

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
if (!$with_ambu) {
  $whereAffect["sejour.type"]         = "!= 'ambu'";
}

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
  $lit = $_affectation->loadRefLit();
  $lit->loadRefChambre()->loadRefService();

  $sejour = $_affectation->loadRefSejour();
  $sejour->loadRefPraticien();
  $patient = $_affectation->_ref_sejour->loadRefPatient();
  $patient->loadIPP();

  $list_affectations[$key]["nom"]          = $patient->nom;
  $list_affectations[$key]["prenom"]       = $patient->prenom;
  $list_affectations[$key]["id"]           = $IPP ? $patient->_IPP : $patient->_id;
  $list_affectations[$key]["service"]      = $lit->_ref_chambre->_ref_service->_id;
  $list_affectations[$key]["chambre"]      = $lit->_ref_chambre->_id;
  $list_affectations[$key]["lit"]          = $lit->_id;
  $list_affectations[$key]["sexe"]         = $patient->sexe;
  $list_affectations[$key]["naissance"]    = CMbDT::format($patient->naissance, "%Y%m%d");
  $list_affectations[$key]["date_entree"]  = CMbDT::format(CMbDT::date($sejour->entree), "%Y%m%d");
  $list_affectations[$key]["heure_entree"] = CMbDT::format(CMbDT::time($sejour->entree), "%H%M");
  $list_affectations[$key]["date_sortie"]  = CMbDT::format(CMbDT::date($sejour->sortie), "%Y%m%d");
  $list_affectations[$key]["heure_sortie"] = CMbDT::format(CMbDT::time($sejour->sortie), "%H%M");
  $list_affectations[$key]["type"]         = $sejour->type;

  if ($detail_lit) {
    $list_affectations[$key]["lit_nom"]       = $lit->nom;
    $list_affectations[$key]["chambre_nom"]   = $lit->_ref_chambre->nom;
    $list_affectations[$key]["nom_naissance"] = $patient->nom_jeune_fille;
  }
}

header("Content-Type: text/plain;");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_affectations", $list_affectations);
$smarty->assign("detail_lit"       , $detail_lit);

$smarty->display("get_etat_lits_txt.tpl");