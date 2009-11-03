<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsEdit();

// Liste des prats
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

// Plage selectionnée
$plage_id = CValue::getOrSession("plage_id", null);
$plage = new CPlageressource;
$plage->load($plage_id);

// Période
$today = mbDate();
$debut = CValue::getOrSession("debut", $today);
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("debut"    , $debut    );
$smarty->assign("prec"     , $prec     );
$smarty->assign("suiv"     , $suiv     );
$smarty->assign("plage"    , $plage    );
$smarty->assign("plages"   , $plages   );
$smarty->assign("listPrat" , $listPrat );
$smarty->assign("listHours", $listHours);

$smarty->display("edit_planning.tpl");