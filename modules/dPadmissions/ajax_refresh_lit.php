<?php 

/**
 * $Id$
 *  
 * @category dPadmissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


CCanDo::checkEdit();

$sejour_id     = CValue::get("sejour_id");
$sortie_reelle = CValue::get("sortie_reelle");

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du séjour
$sejour->loadRefPatient()->loadIPP();
$sejour->loadNDA();
$sejour->loadRefsConsultations();
$sejour->loadRefCurrAffectation();

// Horaire par défaut
$sejour->sortie_reelle = $sortie_reelle;

if (!$sejour->sortie_reelle) {
  $sejour->sortie_reelle = CMbDT::dateTime();
}

$where                = array();
$where["entree"]      = "<= '$sejour->sortie_reelle'";
$where["sortie"]      = ">= '$sejour->sortie_reelle'";
$where["function_id"] = "IS NOT NULL";

$leftjoin = array("affectation" => "affectation.lit_id = lit.lit_id");
$lit = new CLit();

//Lit réservé pour les urgences
$lits_urgence = $lit->loadList($where, null, null, null, $leftjoin);

$where["function_id"] = "IS NULL";
$where["sejour_id"]   = "IS NULL";
$where["lit.lit_id"]  = CSQLDataSource::prepareIn(array_keys($lits_urgence));

//lit qui sont bloqués
$lits_bloque = $lit->loadList($where, null, null, null, $leftjoin);

$affectation = new CAffectation();
unset($where["lit.lit_id"]);
unset($where["sejour_id"]);

/** @var CLit $_lit */
foreach ($lits_urgence as $_lit) {
  $sortie      = CMbDT::transform($affectation->sortie, null, "%Hh%M %d-%m-%Y");
  $_lit->loadRefService()->loadRefsChambres();
  if (array_key_exists($_lit->_id, $lits_bloque)) {
    $_lit->_view .= " (bloqué jusqu'au $sortie)";
    continue;
  }

  //On recherche une affectation d'un patient dans le lit d'urgence
  $where["lit_id"] = "= '$_lit->_id'";
  if (!$affectation->loadObject($where)) {
    continue;
  }

  $patient = $affectation->loadRefSejour()->loadrefPatient();

  // Lit avec un patient
  if ($patient->_id) {
    $_lit->_view .= " (".$patient->_view." (".strtoupper($patient->sexe)."))";
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("lits"  , $lits_urgence);

$smarty->display("inc_form_sortie_lit.tpl");
