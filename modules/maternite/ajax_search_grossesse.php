<?php 

/**
 * $Id$
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$page           = CValue::get("page", 0);
$lastname       = CValue::get("lastname");
$firstname      = CValue::get("firstname");

$terme          = CValue::get("terme_date");
$terme_start    = CValue::get("terme_start");
$terme_end      = CValue::get("terme_end");

$fausse_couche  = CValue::get("fausse_couche");
$multiple       = CValue::get("multiple");

// where
$where = array();
if ($lastname) {
  $where["nom"] = " LIKE '$lastname%' ";
}
if ($firstname) {
  $where["prenom"] = " LIKE '$firstname%' ";
}

if ($terme) {
  $where["terme_prevu"] = " = '$terme'";
}
else {
  if ($terme_start && $terme_end) {
    $where["terme_prevu"] = " BETWEEN '$terme_start' AND '$terme_end' ";
  }
  elseif ($terme_start && !$terme_end) {
    $where["terme_prevu"] = " >= '$terme_start' ";
  }
  elseif (!$terme_start && $terme_end) {
    $where["terme_prevu"] = " <= '$terme_end' ";
  }
}

if ($fausse_couche !== '') {
  $where["fausse_couche"] = " = '$fausse_couche' ";
}
if ($multiple !== '') {
  $where["multiple"] = " = '$multiple' ";
}

$grossesse = new CGrossesse();
$ljoin = array("patients" => "patients.patient_id = grossesse.parturiente_id");
/** @var CGrossesse[] $grossesses */
$nb_grossesses = $grossesse->countList($where, null, $ljoin);
$grossesses = $grossesse->loadList($where, "nom, prenom", "$page, 30", null, $ljoin);

foreach ($grossesses as $_grossesse) {
  $_grossesse->loadRefParturiente();
}

// smarty
$smarty = new CSmartyDP();
$smarty->assign("grossesses", $grossesses);
$smarty->assign("nb_grossesses", $nb_grossesses);
$smarty->assign("page", $page);
$smarty->display("inc_list_search_grossesse.tpl");