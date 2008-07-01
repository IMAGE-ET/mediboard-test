<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $m;

$debutact      = mbGetValueFromGet("debut", mbDate("-1 YEAR"));
$rectif        = mbTransformTime("+0 DAY", $debutact, "%d")-1;
$debutact      = mbDate("-$rectif DAYS", $debutact);
$finact        = mbGetValueFromGet("fin", mbDate());
$rectif        = mbTransformTime("+0 DAY", $finact, "%d")-1;
$finact        = mbDate("-$rectif DAYS", $finact);
$finact        = mbDate("+ 1 MONTH", $finact);
$finact        = mbDate("-1 DAY", $finact);
$prat_id       = mbGetValueFromGet("prat_id", 0);
$salle_id      = mbGetValueFromGet("salle_id", 0);
$discipline_id = mbGetValueFromGet("discipline_id", 0);
$codes_ccam    = strtoupper(mbGetValueFromGet("codes_ccam", ""));

CAppUI::requireModuleFile($m, "inc_graph_activite");
// Finally send the graph to the browser
$graph->render("out",$options);
?>