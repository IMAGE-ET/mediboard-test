<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$discipline_id = CValue::getOrSession("discipline_id");

$g = CGroups::loadCurrent();

// CHargement d'une discipline
$specialite = new CDiscipline;
$specialite->load($discipline_id);
$specialite->loadGroupRefsBack();

//Liste de toutes les disciplines
$listDiscipline = new CDiscipline;
$listDiscipline = $listDiscipline->loadList();
foreach($listDiscipline as &$discipline) {
  $discipline->loadGroupRefsBack();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("group"         , $g             );
$smarty->assign("specialite"    , $specialite    );
$smarty->assign("listDiscipline", $listDiscipline);

$smarty->display("vw_idx_disciplines.tpl");
?>
