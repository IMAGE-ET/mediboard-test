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

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "DESC");
$order_col = CValue::getOrSession("order_col", "ccmu");

// Récuperation du medecin sélectionné
$medecin = new CMedecin();
if (CValue::get("new", 0) || $dialog) {
  $medecin->load(null);
  CValue::setSession("medecin_id", null);
}
else if ($medecin->load($medecin_id)) {
  $medecin->countPatients();
}

$g = CValue::getOrSessionAbs("g", CAppUI::$instance->user_group);
$indexGroup = new CGroups;
$indexGroup->load($g);


// Récuperation des médecins recherchés
if ($dialog) {
  $medecin_nom    = CValue::get("medecin_nom"   , ""  );
  $medecin_prenom = CValue::get("medecin_prenom", ""  );
  $medecin_cp     = CValue::get("medecin_cp"    , $indexGroup->_cp_court);
  $medecin_type   = CValue::get("medecin_type"  , "medecin");
}
else {
  $medecin_nom    = CValue::getOrSession("medecin_nom");
  $medecin_prenom = CValue::getOrSession("medecin_prenom");
  $medecin_cp     = CValue::getOrSession("medecin_cp", $indexGroup->_cp_court);
  $medecin_type   = CValue::getOrSession("medecin_type", "medecin");
}

$where = array();

if ($medecin_nom) {
  $where["nom"]      = "LIKE '".addslashes($medecin_nom)."%'";
}

if ($medecin_prenom) {
  $where["prenom"]   = "LIKE '".addslashes($medecin_prenom)."%'";
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
$medecins = $medecins->loadList($where, $order, "0, 50");

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

$smarty->display("vw_medecins.tpl");
