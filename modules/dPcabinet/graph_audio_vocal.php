<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $exam_audio;

$exam_audio = new CExamAudio;
$exam_audio->load(mbGetValueFromGetOrSession("examaudio_id"));

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_vocal"));
$graph_vocal->Stroke();