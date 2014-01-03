<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$date       = CValue::getOrSession("date", CMbDT::date());
$plageop_id = CValue::getOrSession("plageop_id");

// Informations sur la plage demandée
$plagesel = new CPlageOp;
$plagesel->load($plageop_id);
$plagesel->loadRefSalle();

$listBlocs       = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$default_bloc_id = $plagesel->_ref_salle->bloc_id ? $plagesel->_ref_salle->bloc_id : reset($listBlocs)->_id;
$bloc_id         = CValue::getOrSession("bloc_id", $default_bloc_id);

if (!array_key_exists($bloc_id, $listBlocs)) {
  $bloc_id = reset($listBlocs)->_id;
}

$listSalles = array();

foreach ($listBlocs as &$curr_bloc) {
  $curr_bloc->loadRefsSalles();
}

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();

$listSalles = $bloc->_ref_salles;
  

if (!$plagesel->temps_inter_op) {
  $plagesel->temps_inter_op = "00:00:00";
}
if ($plagesel->_id) {
  $arrKeySalle = array_keys($listSalles);
  if (!in_array($plagesel->salle_id, $arrKeySalle) || $plagesel->date != $date) {
    $plageop_id = 0;
    $plagesel = new CPlageOp;
  }
  $plagesel->loadAffectationsPersonnel();
}

if (!$plagesel->_id) {
  $plagesel->debut = CPlageOp::$hours_start.":00:00";
  $plagesel->fin   = CPlageOp::$hours_start.":00:00";
}

// On charge le praticien et ses fonctions secondaires
$chir = $plagesel->loadRefChir();
$chir->loadRefFunction();
$_functions = $chir->loadBackRefs("secondary_functions");

// Liste des Specialités
$function = new CFunctions();
$specs = $function->loadSpecialites(PERM_READ, 1);

// Liste des Anesthésistes
$mediuser = new CMediusers();
$anesths = $mediuser->loadAnesthesistes();
CMbObject::massLoadFwdRef($anesths, "function_id");
foreach ($anesths as $_anesth) {
  $_anesth->loadRefFunction();
}

// Liste des praticiens
$chirs = $mediuser->loadChirurgiens();
CMbObject::massLoadFwdRef($chirs, "function_id");
foreach ($chirs as $_chir) {
  $_chir->loadRefFunction();
}

// Chargement du personnel
$listPers = array(
  "iade"         => CPersonnel::loadListPers("iade"),
  "op"           => CPersonnel::loadListPers("op"),
  "op_panseuse"  => CPersonnel::loadListPers("op_panseuse"),
  "sagefemme"    => CPersonnel::loadListPers("sagefemme"),
  "manipulateur" => CPersonnel::loadListPers("manipulateur")
);

if ($plagesel->_id) {
  $plagesel->getNbOperations();
  $plagesel->getNbOperationsAnnulees();
  $listPers = $plagesel->loadPersonnelDisponible($listPers);
}

//Création du template
$smarty = new CSmartyDP();

$smarty->assign("listBlocs" , $listBlocs);
$smarty->assign("bloc"      , $bloc);
$smarty->assign("date"      , $date);
$smarty->assign("plagesel"  , $plagesel);
$smarty->assign("specs"     , $specs);
$smarty->assign("anesths"   , $anesths);
$smarty->assign("chirs"     , $chirs);
$smarty->assign("listPers"  , $listPers);
$smarty->assign("_functions", $_functions);

$smarty->display("inc_edit_planning.tpl");
