<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

require_once($AppUI->getModuleFile($m, "inc_vw_affectations"));

$can->needsRead();

$deb     = mbGetValueFromGet("deb"    , date("Y-m-d")." 06:00:00");
$fin     = mbGetValueFromGet("fin"    , date("Y-m-d")." 21:00:00");
$service = mbGetValueFromGet("service", 0                        );
$type    = mbGetValueFromGet("type"   , 0                        );
$chir    = mbGetValueFromGet("chir"   , 0                        );
$spe     = mbGetValueFromGet("spe"    , 0                        );
$conv    = mbGetValueFromGet("conv"   , 0                        );
$ordre   = mbGetValueFromGet("ordre"  , "heure"                  );
$total   = 0;

$sejours = new CSejour;

$sejourReq = new CRequest;

$sejourReq->addLJoinClause("patients", "patients.patient_id = sejour.patient_id");

$sejourReq->addWhereClause("sejour.entree_prevue", "BETWEEN '$deb' AND '$fin'");
$sejourReq->addWhereClause("sejour.group_id", "= '$g'");
$sejourReq->addWhereClause("sejour.annule", "= '0'");

// Clause de filtre par spécialité / chir
if ($spe or $chir) {
  $speChirs = new CMediusers;
  $speChirs = $speChirs->loadList(array ("function_id" => "= '$spe'"));
  $sejourReq->addWhereClause("sejour.praticien_id", db_prepare_in(array_keys($speChirs), $chir));
}

if ($type) {
  $sejourReq->addWhereClause("sejour.type", "= '$type'");
}

if ($conv == "o") {
  $sejourReq->addWhereClause(null, "(sejour.convalescence IS NOT NULL AND sejour.convalescence != '')");
}

if ($conv == "n") {
  $sejourReq->addWhereClause(null, "(sejour.convalescence IS NULL OR sejour.convalescence = '')");
}

$sejourReq->addOrder("DATE(sejour.entree_prevue)");
$sejourReq->addOrder("sejour.praticien_id");

if($ordre == "heure") {
  $sejourReq->addOrder("sejour.entree_prevue");
} else {
  $sejourReq->addOrder("patients.nom");
  $sejourReq->addOrder("patients.prenom");
}

$sejours = $sejours->loadListByReq($sejourReq);

$listDays = array();
$listPrats = array();

// Liste des services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

foreach ($sejours as $key => &$sejour) {
  $sejour->loadRefsAffectations();
  $sejour->loadRefsOperations();
  $sejour->loadRefPatient();
  $sejour->_ref_first_affectation->loadRefLit();
  $sejour->_ref_first_affectation->_ref_lit->loadCompleteView();

  if ($service && ($sejour->_ref_first_affectation->_ref_lit->_ref_chambre->service_id != $service)) {
    unset($sejours[$key]);
    continue;
  }elseif(!$service && $sejour->_ref_first_affectation->affectation_id && !in_array($sejour->_ref_first_affectation->_ref_lit->_ref_chambre->service_id,array_keys($services))){
    unset($sejours[$key]);
    continue;
  } 

  $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);

  foreach($sejour->_ref_operations as &$operation) {
    $operation->loadRefsFwd();
  }

  $curr_date = mbDate(null, $sejour->entree_prevue);
  $curr_prat = $sejour->praticien_id;
  $listDays[$curr_date][$curr_prat]["praticien"] =& $sejour->_ref_praticien;
  $listDays[$curr_date][$curr_prat]["sejours"][] =& $sejour;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("deb"      , $deb           );
$smarty->assign("fin"      , $fin           );
$smarty->assign("listDays" , $listDays      );
$smarty->assign("listPrats", $listPrats     );
$smarty->assign("total"    , count($sejours));

$smarty->display("print_planning.tpl");

?>