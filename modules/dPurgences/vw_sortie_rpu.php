<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

// Type d'affichage
$aff_sortie = CValue::postOrSession("aff_sortie","tous");

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_pec_transport");

// Chargement des urgences prises en charge
$ljoin = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";

// Selection de la date
$date = CValue::getOrSession("date", mbDate());
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = mbDate("-$date_tolerance DAY", $date);
$date_after  = mbDate("+1 DAY", $date);
$where = array();
$where[] = "sejour.entree_reelle BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree_reelle BETWEEN '$date_before' AND '$date_after')";

// RPU Existants
$where[] = "sejour.type = 'urg' OR rpu.sejour_id";
$where["rpu.rpu_id"] = "IS NOT NULL";

if ($aff_sortie == "sortie"){
  $where["sortie_reelle"] = "IS NULL";
}

$order_col = "_pec_transport";
$order = "consultation.heure $order_way";

$sejour = new CSejour;
$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->loadNumDossier();
  $_sejour->loadRefsConsultations();
  $_sejour->_veille = mbDate($_sejour->entree_reelle) != $date;
  
	// Détail du RPU
	$rpu =& $_sejour->_ref_rpu;
  $rpu->loadRefSejourMutation();
  $rpu->_ref_consult->loadRefsActes();
   
  // Détail du patient
	$patient =& $_sejour->_ref_patient; 
  $patient->loadIPP();

}

// Chargement des etablissements externes
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, "nom");

// Chargement des services
$service = new CService();
$services = $service->loadList(null, "nom");

// Contraintes sur le mode de sortie / destination
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"] = array("", "FUGUE", "SCAM", "PSA", "REO");

// Praticiens urgentistes
$group = CGroups::loadCurrent();

$listPrats = $AppUI->_ref_user->loadPraticiens(PERM_READ, $group->service_urgences_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);
$smarty->assign("listEtab", $listEtab);
$smarty->assign("services", $services);
$smarty->assign("order_col" , $order_col);
$smarty->assign("order_way" , $order_way);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("aff_sortie", $aff_sortie);
$smarty->assign("listPrats", $listPrats);
$smarty->assign("date", $date);
$smarty->assign("today", mbDate());

$smarty->display("vw_sortie_rpu.tpl");
?>