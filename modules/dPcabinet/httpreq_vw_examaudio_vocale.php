<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $m;
global $frequences, $pressions, $exam_audio;


$examaudio_id = mbGetValueFromGetOrSession("examaudio_id");

$exam_audio = new CExamAudio;
$exam_audio->load($examaudio_id);

CAppUI::requireModuleFile($m, "inc_graph_audio_vocal");
AudiogrammeVocal::$graph->Stroke("tmp/graphtmp.png");
$map_vocal = AudiogrammeVocal::$graph->GetHTMLImageMap("graph_vocal");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("map_vocal" , $map_vocal);
$smarty->assign("exam_audio", $exam_audio);
$smarty->assign("time"      , time());

$smarty->display("inc_exam_audio/inc_examaudio_graph_vocale.tpl");
?>