<?php /* $Id: view_compta.php 331 2006-07-13 14:26:26Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 331 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Statistiques sur les id400
$req = new CRequest;
$req->addTable("id_sante400");
$req->addColumn("COUNT(DISTINCT object_id)", "nbObjects");
$req->addColumn("COUNT(id_sante400_id)", "nbID400s");
$statsTotal = db_loadList($req->getRequest());
$statsTotal = $statsTotal[0];

$req->addSelect("object_class");
$req->addGroup("object_class");
$stats = db_loadList($req->getRequest());

// Computes average ID400 count per object
foreach ($stats as &$stat) {
  $stat["average"] = $stat["nbID400s"] / $stat["nbObjects"];
}

$statsTotal["average"] = $statsTotal["nbID400s"] / $statsTotal["nbObjects"];


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("stats", $stats);
$smarty->assign("statsTotal", $statsTotal);
$smarty->display("stats_identifiants.tpl");

?>