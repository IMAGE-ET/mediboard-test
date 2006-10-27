<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m, $exam_audio;

$exam_audio = new CExamAudio;
$exam_audio->load(mbGetValueFromGetOrSession("examaudio_id"));

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_tonal"));

$side = dPgetParam($_GET, "side");

$graphname = "graph_tonal_{$side}";
$graph = $$graphname;
$graph->Stroke();
