<?php /* $Id: graph_audio_tympan.php,v 1.1 2005/12/19 19:24:06 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPcabinet", "examaudio"));

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
$where["consultation_id"] = "= '$consultation_id'";
$exam_audio = new CExamAudio;
$exam_audio->loadObject($where);

require_once( $AppUI->getModuleFile("$m", "inc_graph_audio_tympan"));

$side = dPgetParam($_GET, "side");

$graphname = "graph_tympan_{$side}";
$graph = $$graphname;
$graph->Stroke();
