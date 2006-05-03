<?php /* $Id: graph_audio_tonal.php,v 1.5 2005/12/19 19:24:06 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPcabinet", "examaudio"));

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
$where["consultation_id"] = "= '$consultation_id'";
$exam_audio = new CExamAudio;
$exam_audio->loadObject($where);

require_once( $AppUI->getModuleFile("$m", "inc_graph_audio_tonal"));

$side = dPgetParam($_GET, "side");

$graphname = "graph_tonal_{$side}";
$graph = $$graphname;
$graph->Stroke();
