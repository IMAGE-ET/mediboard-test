<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$_conduction = mbGetValueFromGetOrSession("_conduction", "aerien");
$_oreille = mbGetValueFromGetOrSession("_oreille", "gauche");

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
$where["consultation_id"] = "= '$consultation_id'";
$exam_audio = new CExamAudio;
$exam_audio->loadObject($where);
$exam_audio->consultation_id = $consultation_id;
$exam_audio->loadRefs();
$exam_audio->_ref_consult->loadRefsFwd();


require_once($AppUI->getModuleFile("$m", "inc_graph_audio_tonal"));

$graph_tonal_gauche->Stroke("tmp/graphtmp.png");
$map_tonal_gauche = $graph_tonal_gauche->GetHTMLImageMap("graph_tonal_gauche");

$graph_tonal_droite->Stroke("tmp/graphtmp.png");
$map_tonal_droite = $graph_tonal_droite->GetHTMLImageMap("graph_tonal_droite");

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_tympan"));
$graph_tympan_gauche->Stroke("tmp/graphtmp.png");
$map_tympan_gauche = $graph_tympan_gauche->GetHTMLImageMap("graph_tympan_gauche");

$graph_tympan_droite->Stroke("tmp/graphtmp.png");
$map_tympan_droite = $graph_tympan_droite->GetHTMLImageMap("graph_tympan_droite");

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_vocal"));
$graph_vocal->Stroke("tmp/graphtmp.png");
$map_vocal = $graph_vocal->GetHTMLImageMap("graph_vocal");

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
$smarty = new CSmartyDP(1);

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


$smarty->display('exam_audio.tpl');

?>