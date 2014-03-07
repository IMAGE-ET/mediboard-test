<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$dialog     = CValue::get("dialog");
$medecin_id = CValue::getOrSession("medecin_id");
$g = CValue::getOrSessionAbs("g", CAppUI::$instance->user_group);

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "DESC");
$order_col = CValue::getOrSession("order_col", "ccmu");

// pagination
$start_med          = CValue::get("start_med", 0);
$step_med           = CValue::get("step_med", 20);

$medecin = new CMedecin();
$ds = $medecin->getDS();

$indexGroup = new CGroups;
$indexGroup->load($g);


// Récuperation des médecins recherchés
if ($dialog) {
  $medecin_nom    = CValue::get("nom"     , "");
  $medecin_prenom = CValue::get("prenom"  , "");
  $medecin_cp     = CValue::get("cp");
  $medecin_ville  = CValue::get("ville");
  $medecin_type   = CValue::get("type"    , "medecin");
  $medecin_disciplines   = CValue::get("disciplines");
}
else {
  $medecin_nom    = CValue::getOrSession("nom");
  $medecin_prenom = CValue::getOrSession("prenom");
  $medecin_cp     = CValue::getOrSession("cp");
  $medecin_ville  = CValue::getOrSession("ville");
  $medecin_type   = CValue::getOrSession("type", "medecin");
  $medecin_disciplines   = CValue::getOrSession("disciplines");
}

$where = array();

if ($medecin_nom) {
  $where["nom"]      = $ds->prepareLike("%$medecin_nom%");
}

if ($medecin_prenom) {
  $where["prenom"]   = $ds->prepareLike("%$medecin_prenom%");
}

if ($medecin_disciplines) {
  $where["disciplines"]   = $ds->prepareLike("%$medecin_disciplines%");
}


if ($medecin_cp && $medecin_cp != "00") {
  $cps = preg_split("/\s*[\s\|,]\s*/", $medecin_cp);
  CMbArray::removeValue("", $cps);
  
  $where_cp = array();
  foreach ($cps as $cp) {
    $where_cp[] = "cp LIKE '".$cp."%'";
  }
  
  $where[] = implode(" OR ", $where_cp);
}

if ($medecin_ville) {
  $where["ville"]   = $ds->prepareLike("%$medecin_ville%");
}

if ($medecin_type) {
  $where["type"]     = "= '$medecin_type'";
}

$order = "nom, prenom";

if ($order_col == "cp") {
  $order = "cp $order_way, nom, prenom";
}
else if ($order_col == "ville") {
  $order = "ville $order_way, nom, prenom";
}

$medecins = new CMedecin();

$count_medecins = $medecins->countList($where);
$medecins = $medecins->loadList($where, $order, "$start_med, $step_med");

$list_types = $medecin->_specs['type']->_locales;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dialog"     , $dialog);
$smarty->assign("nom"        , $medecin_nom);
$smarty->assign("prenom"     , $medecin_prenom);
$smarty->assign("cp"         , $medecin_cp);
$smarty->assign("type"       , $medecin_type);
$smarty->assign("medecins"   , $medecins);
$smarty->assign("medecin"    , $medecin);
$smarty->assign("list_types" , $list_types);
$smarty->assign("count_medecins", $count_medecins);
$smarty->assign("order_col"   , $order_col);
$smarty->assign("order_way"   , $order_way);
$smarty->assign("start_med", $start_med);
$smarty->assign("step_med", $step_med);

$smarty->display("vw_medecins.tpl");
