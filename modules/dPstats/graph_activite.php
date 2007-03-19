<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$debutact      = mbGetValueFromGetOrSession("debut", mbDate("-1 YEAR"));
$rectif        = mbTranformTime("+0 DAY", $debutact, "%d")-1;
$debutact      = mbDate("-$rectif DAYS", $debutact);
$finact        = mbGetValueFromGetOrSession("fin", mbDate());
$rectif        = mbTranformTime("+0 DAY", $finact, "%d")-1;
$finact        = mbDate("-$rectif DAYS", $finact);
$finact        = mbDate("+ 1 MONTH", $finact);
$finact        = mbDate("-1 DAY", $finact);
$prat_id       = mbGetValueFromGetOrSession("prat_id", 0);
$salle_id      = mbGetValueFromGetOrSession("salle_id", 0);
$discipline_id = mbGetValueFromGetOrSession("discipline_id", 0);
$codeCCAM      = strtoupper(mbGetValueFromGetOrSession("codeCCAM", ""));

require_once($AppUI->getModuleFile($m, "inc_graph_activite"));

// Finally send the graph to the browser
$graph->Stroke();
?>