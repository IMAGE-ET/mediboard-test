<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("admin"));
require_once($AppUI->getModuleClass("mediusers", "discipline"));

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$discipline_id = mbGetValueFromGetOrSession("discipline_id");

// CHargement d'une discipline
$specialite = new CDiscipline;
$specialite->load($discipline_id);
$specialite->loadRefsBack();

//Liste de toutes les disciplines
$listDiscipline = new CDiscipline;
$listDiscipline = $listDiscipline->loadList();


// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("specialite"    , $specialite    );
$smarty->assign("listDiscipline", $listDiscipline);

$smarty->display("vw_idx_disciplines.tpl");
?>
