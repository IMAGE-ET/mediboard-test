<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkEdit();

// Liste des prats
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

// P�riode
$today = mbDate();
$debut = CValue::getOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

// Plage selectionn�e
$plage_id = CValue::getOrSession("plage_id", null);
$plage = new CPlageressource;
$plage->date = $debut;
$plage->load($plage_id);
$plage->loadRefsNotes();

// S�lection des plages
$plages = array();
for ($i = 0; $i < 7; $i++) {
  $date = mbDate("+$i day", $debut);
  $where["date"] = "= '$date'";
  $plagesPerDay = $plage->loadList($where);
  foreach ($plagesPerDay as $_plage) {
    $_plage->loadRefs();
  }
  $plages[$date] = $plagesPerDay;
}

// Liste des heures
for ($i = 8; $i <= 20; $i++) {
  $listHours[$i] = $i;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("debut"    , $debut    );
$smarty->assign("prec"     , $prec     );
$smarty->assign("suiv"     , $suiv     );
$smarty->assign("plage"    , $plage    );
$smarty->assign("plages"   , $plages   );
$smarty->assign("listPrat" , $listPrat );
$smarty->assign("listHours", $listHours);

$smarty->display("edit_planning.tpl");