<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsEdit();

// Statistiques sur les id400
$req = new CRequest;
$req->addTable("id_sante400");
$req->addColumn("COUNT(DISTINCT object_id)", "nbObjects");
$req->addColumn("COUNT(id_sante400_id)", "nbID400s");

$ds = CSQLDataSource::get("std");
$statTotal = $ds->loadList($req->getRequest());
$statTotal = $statTotal[0];

$req->addSelect("object_class");
$req->addGroup("object_class");
$stats = $ds->loadList($req->getRequest());

// Computes average ID400 count per object
foreach ($stats as &$stat) {
  $stat["average"] = $stat["nbID400s"] / $stat["nbObjects"];
}

$statTotal["average"] = @($statTotal["nbID400s"] / $statTotal["nbObjects"]);


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("stats", $stats);
$smarty->assign("statTotal", $statTotal);
$smarty->display("stats_identifiants.tpl");

?>