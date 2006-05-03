<?php /* $Id: view_planning.php,v 1.3 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 1.3 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPressources', 'plageressource') );
require_once( $AppUI->getModuleClass('mediusers') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Période
$today = mbDate();
$debut = mbGetValueFromGetOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

// Sélection des plages
$plages = array();
$curr_plage = new CPlageressource();
for ($i = 0; $i < 7; $i++) {
  $date = mbDate("+$i day", $debut);
  $where["date"] = "= '$date'";
  $plagesPerDay = $curr_plage->loadList($where);
  foreach($plagesPerDay as $key => $value) {
    $plagesPerDay[$key]->loadRefs();
  }
  $plages[$date] = $plagesPerDay;
}

// Liste des heures
for ($i = 8; $i <= 20; $i++) {
  $listHours[$i] = $i;
}

// Etat du compte
$prat = new CMediusers;
$prat->load($AppUI->user_id);
$compte = array();
$isprat = $prat->isPraticien();
if($isprat) {
  $order = "date";
  // Plages impayées
  $sql = "SELECT COUNT(plageressource_id) AS total," .
      "\nSUM(tarif) AS somme" .
      "\nFROM plageressource" .
      "\nWHERE prat_id = '$prat->user_id'" .
      "\nAND date < '".mbDate()."'" .
      "\nAND paye = 0";
  $result = db_loadlist($sql);
  $compte["impayes"]["total"] = $result[0]["total"];
  $compte["impayes"]["somme"] = $result[0]["somme"];
  $compte["impayes"]["plages"] = new CPlageressource;
  $where = array();
  $where["prat_id"] = "= '$prat->user_id'";
  $where["date"] = "< '".mbDate()."'";
  $where["paye"] = "= 0";
  $compte["impayes"]["plages"] = $compte["impayes"]["plages"]->loadList($where, $order);
  // Plages bloquées
  $sql = "SELECT COUNT(plageressource_id) AS total," .
      "\nSUM(tarif) AS somme" .
      "\nFROM plageressource" .
      "\nWHERE prat_id = '$prat->user_id'" .
      "\nAND date BETWEEN '".mbDate()."' AND '".mbDate("+15 DAYS")."'";
  $result = db_loadlist($sql);
  $compte["inf15"]["total"] = $result[0]["total"];
  $compte["inf15"]["somme"] = $result[0]["somme"];
  $compte["inf15"]["plages"] = new CPlageressource;
  $where = array();
  $where["prat_id"] = "= '$prat->user_id'";
  $where["date"] = "BETWEEN '".mbDate()."' AND '".mbDate("+15 DAYS")."'";
  $compte["inf15"]["plages"] = $compte["inf15"]["plages"]->loadList($where, $order);
  // Plages réservées
  $sql = "SELECT COUNT(plageressource_id) AS total," .
      "\nSUM(tarif) AS somme" .
      "\nFROM plageressource" .
      "\nWHERE prat_id = '$prat->user_id'" .
      "\nAND date > '".mbDate("+15 DAYS")."'";
  $result = db_loadlist($sql);
  $compte["sup15"]["total"] = $result[0]["total"];
  $compte["sup15"]["somme"] = $result[0]["somme"];
  $compte["sup15"]["plages"] = new CPlageressource;
  $where = array();
  $where["prat_id"] = "= '$prat->user_id'";
  $where["date"] = "> '".mbDate("+15 DAYS")."'";
  $compte["sup15"]["plages"] = $compte["sup15"]["plages"]->loadList($where, $order);
}

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('debut', $debut);
$smarty->assign('prec', $prec);
$smarty->assign('suiv', $suiv);
$smarty->assign('plages', $plages);
$smarty->assign('prat', $prat);
$smarty->assign('isprat', $isprat);
$smarty->assign('compte', $compte);
$smarty->assign('listHours', $listHours);

$smarty->display('view_planning.tpl');