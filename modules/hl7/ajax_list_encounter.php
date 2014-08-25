<?php

/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Récuperation des patients recherchés
$page             = CValue::get("page", 0);
$prenom           = trim(CValue::getOrSession("prenom"));
$nom              = trim(CValue::getOrSession("nom"));
$nom_jeune_fille  = trim(CValue::getOrSession("nom_jeune_fille"));
$sexe             = CValue::get("sexe", "");
$code_pays        = CValue::get("pays");
//$date_creation    = CValue::get("date_creation");
//$createur         = CValue::get("createur");

//where
$where = array();
$where[]         = "`nom` LIKE '$nom%' OR `nom_jeune_fille` LIKE '$nom%'";
$where["prenom"] = "LIKE '$prenom%'";
$where["nom"]    = "LIKE '$nom%'";

if ($sexe != "") {
  $where["sexe"] = "= '$sexe'";
}

if ($code_pays != "") {
  $where["pays"] = "= '$code_pays'";
}

$order      = "nom, prenom, naissance";
$limit_list = 30;
$limit      = "$page, $limit_list";

$patient = new CPatient();
$patient->nom             = $nom;
$patient->prenom          = $prenom;
$patient->nom_jeune_fille = $nom_jeune_fille;
$patient->sexe            = $sexe;
$patient->pays            = $code_pays;

$nb_pat   = $patient->countList($where);
$patients = $patient->loadList($where, $order, $limit);

$smarty = new CSmartyDP();
$smarty->assign("nb_pat"  , $nb_pat);
$smarty->assign("page"    , $page);
$smarty->assign("patients", $patients);
$smarty->assign("patient" , $patient);
//$smarty->assign("patient_date_creation", $date_creation);
//$smarty->assign("patient_createur", $createur);
$smarty->display("inc_list_encounter.tpl");