<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();
$group = CGroups::loadCurrent();

$date = CValue::getOrSession("date", CMbDT::date());
$page = CValue::get("page", 0);
$next = CMbDT::date("+1 day", $date);

$sejour = new CSejour;
$where = array();
$where["entree"] = "< '$next'";
$where["sortie"] = "> '$date'";
$where["group_id"]      = "= '$group->_id'";
$where["annule"]        = "= '0'";
$order = array();
$order[] = "sortie";
$order[] = "entree";
$step = 30;
$limit = "$page,$step";

/** @var CSejour[] $listSejours */
$count = $sejour->countList($where);
$listSejours = $sejour->loadList($where, $order, $limit);

$patients = CSejour::massLoadFwdRef($listSejours, "patient_id");
$ipps = CPatient::massLoadIPP($patients);
$ndas = CSejour::massLoadNDA($listSejours);
$praticiens = CSejour::massLoadFwdRef($listSejours, "praticien_id");
CMediusers::massLoadFwdRef($praticiens, "function_id");
CSejour::massLoadFwdRef($listSejours, "group_id");
CSejour::massLoadFwdRef($listSejours, "etablissement_sortie_id");
CSejour::massLoadFwdRef($listSejours, "service_sortie_id");
CSejour::massLoadFwdRef($listSejours, "service_sortie_id");

foreach ($listSejours as $_sejour) {
  $_sejour->_ref_patient = $patients[$_sejour->patient_id];
  $_sejour->loadRefPraticien();
  $_sejour->loadExtCodesCCAM();
  $_sejour->loadRefsFactureEtablissement();
  $_sejour->countActes();
  $_sejour->loadRefTraitementDossier();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"       , $date);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("page"       , $page);
$smarty->assign("count"      , $count);
$smarty->assign("step"       , $step);

$smarty->display("current_dossiers/inc_current_sejours.tpl");
