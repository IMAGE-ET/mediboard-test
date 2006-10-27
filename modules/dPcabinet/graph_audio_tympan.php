<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m, $exam_audio, $graph_tympan_gauche, $graph_tympan_droite;

$exam_audio = new CExamAudio;
$exam_audio->load(mbGetValueFromGetOrSession("examaudio_id"));

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_tympan"));

$side = dPgetParam($_GET, "side");

$graphname = "graph_tympan_{$side}";
$graph = $$graphname;
$graph->Stroke();
