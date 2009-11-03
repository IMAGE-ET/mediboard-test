<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $m, $exam_audio;

$exam_audio = new CExamAudio;
$exam_audio->load(CValue::getOrSession("examaudio_id"));

CAppUI::requireModuleFile($m, "inc_graph_audio_tonal");

$side = CValue::get("side");

AudiogrammeTonal::${$side}->Stroke();
