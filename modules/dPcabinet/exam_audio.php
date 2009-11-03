<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $m;
global $frequences, $pressions, $exam_audio;

$can->needsEdit();

$_conduction = CValue::getOrSession("_conduction", "aerien");
$_oreille = CValue::getOrSession("_oreille", "gauche");

$consultation_id = CValue::getOrSession("consultation_id");
$where = array("consultation_id" => "= '$consultation_id'");
$exam_audio = new CExamAudio;
$exam_audio->loadObject($where);

if (!$exam_audio->_id) {
  $exam_audio->consultation_id = $consultation_id;
  $exam_audio->store();
}

$exam_audio->loadRefs();
$exam_audio->_ref_consult->loadRefsFwd();
$exam_audio->loadAides($exam_audio->_ref_consult->_ref_plageconsult->chir_id);

CAppUI::requireModuleFile($m, "inc_graph_audio_tonal");
AudiogrammeTonal::$gauche->Stroke("tmp/graphtmp.png");
$map_tonal_gauche = AudiogrammeTonal::$gauche->GetHTMLImageMap("graph_tonal_gauche");

AudiogrammeTonal::$droite->Stroke("tmp/graphtmp.png");
$map_tonal_droite = AudiogrammeTonal::$droite->GetHTMLImageMap("graph_tonal_droite");


CAppUI::requireModuleFile($m, "inc_graph_audio_tympan");
AudiogrammeTympano::$gauche->Stroke("tmp/graphtmp.png");
$map_tympan_gauche = AudiogrammeTympano::$gauche->GetHTMLImageMap("graph_tympan_gauche");

AudiogrammeTympano::$droite->Stroke("tmp/graphtmp.png");
$map_tympan_droite = AudiogrammeTympano::$droite->GetHTMLImageMap("graph_tympan_droite");


CAppUI::requireModuleFile($m, "inc_graph_audio_vocal");
AudiogrammeVocal::$graph->Stroke("tmp/graphtmp.png");
$map_vocal = AudiogrammeVocal::$graph->GetHTMLImageMap("graph_vocal");

$bilan = array();
foreach ($exam_audio->_gauche_osseux as $index => $perte) {
  $bilan[$frequences[$index]]["osseux"]["gauche"] = $perte;
}
foreach ($exam_audio->_gauche_aerien as $index => $perte) {
  $bilan[$frequences[$index]]["aerien"]["gauche"] = $perte;
}
foreach ($exam_audio->_droite_osseux as $index => $perte) {
  $bilan[$frequences[$index]]["osseux"]["droite"] = $perte;
}
foreach ($exam_audio->_droite_aerien as $index => $perte) {
  $bilan[$frequences[$index]]["aerien"]["droite"] = $perte;
}

foreach ($bilan as $frequence => $value) {
  $pertes =& $bilan[$frequence];
  foreach ($pertes as $keyConduction => $valConduction) {
    $conduction =& $pertes[$keyConduction];
    $conduction["delta"] = $conduction["droite"] - $conduction["gauche"];
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_conduction", $_conduction);
$smarty->assign("_oreille", $_oreille);
$smarty->assign("frequences", $frequences);
$smarty->assign("pressions", $pressions);
$smarty->assign("exam_audio", $exam_audio);
$smarty->assign("bilan", $bilan);
$smarty->assign("map_tonal_gauche", $map_tonal_gauche);
$smarty->assign("map_tonal_droite", $map_tonal_droite);
$smarty->assign("map_tympan_gauche", $map_tympan_gauche);
$smarty->assign("map_tympan_droite", $map_tympan_droite);
$smarty->assign("map_vocal", $map_vocal);
$smarty->assign("time"     , time());


$smarty->display('exam_audio.tpl');

?>