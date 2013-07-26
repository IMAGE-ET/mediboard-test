<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

global $m;
global $exam_audio;

$examaudio_id = CValue::getOrSession("examaudio_id");

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
