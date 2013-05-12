<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Type d'affichage
$view_sortie = CValue::get("view_sortie","tous");

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_pec_transport");

// Chargement des urgences prises en charge
$ljoin = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";

// Selection de la date
$date = CValue::get("date");
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = CMbDT::date("-$date_tolerance DAY", $date);
$date_after  = CMbDT::date("+1 DAY", $date);
$where = array();
$group = CGroups::loadCurrent();
$where["group_id"] = " = '$group->_id'";
$where[] = "sejour.entree BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after')";

// RPU Existants
$where["rpu.rpu_id"] = "IS NOT NULL";

if ($view_sortie == "sortie") {
  $where["sortie_reelle"] = "IS NULL";
}

if (in_array($view_sortie, array("normal", "mutation", "transfert", "deces"))) {
  $where["sortie_reelle"] = "IS NOT NULL";
  $where["mode_sortie"] = "= '$view_sortie'";
}

$order = "consultation.heure $order_way";

$sejour = new CSejour;

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->loadNDA();
  $_sejour->loadRefsConsultations();
  $_sejour->_veille = CMbDT::date($_sejour->entree) != $date;
  
  // Détail du RPU
  $rpu =& $_sejour->_ref_rpu;
  $rpu->loadRefSejourMutation();
  $rpu->_ref_consult->loadRefsActes();
   
  // Détail du patient
  $patient =& $_sejour->_ref_patient; 
  $patient->loadIPP();
}

// Chargement des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date"       , $date);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("services"   , $services);
$smarty->assign("print"      , true);
$smarty->display("print_sortie_patients.tpl");
