<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $m;
global $frequences, $pressions, $exam_audio;


$examaudio_id = mbGetValueFromGetOrSession("examaudio_id");

$exam_audio = new CExamAudio;
$exam_audio->load($examaudio_id);

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_vocal"));
$graph_vocal->Stroke("tmp/graphtmp.png");
$map_vocal = $graph_vocal->GetHTMLImageMap("graph_vocal");

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("map_vocal" , $map_vocal);
$smarty->assign("exam_audio", $exam_audio);
$smarty->assign("time"      , time());

$smarty->display("inc_exam_audio/inc_examaudio_graph_vocale.tpl");
?>