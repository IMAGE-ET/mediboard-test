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
global $exam_audio, $reloadGraph;

$examaudio_id = CValue::getOrSession("examaudio_id");
$side         = CValue::getOrSession("side");
$reloadGraph  = $side;

$exam_audio = new CExamAudio;
$exam_audio->load($examaudio_id);

CAppUI::requireModuleFile($m, "inc_graph_audio_tonal");

AudiogrammeTonal::${$side}->Stroke("tmp/graphtmp.png");
$map_tonal = AudiogrammeTonal::${$side}->GetHTMLImageMap("graph_tonal_".$side);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("map_tonal" , $map_tonal);
$smarty->assign("side"      , $side);
$smarty->assign("fctOnClick", ucwords($side));
$smarty->assign("exam_audio", $exam_audio);
$smarty->assign("time"    , time());

$smarty->display("inc_exam_audio/inc_examaudio_graph_tonal.tpl");
