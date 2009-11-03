<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Thomas Despoix
*/

global $can, $m, $exam_audio;

$exam_audio = new CExamAudio;
$exam_audio->load(CValue::getOrSession("examaudio_id"));

CAppUI::requireModuleFile($m, "inc_graph_audio_vocal");

AudiogrammeVocal::$graph->Stroke();