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

$frequences = CExamAudio::$frequences;

$examaudio_id = CValue::getOrSession("examaudio_id");

$exam_audio = new CExamAudio;
$exam_audio->load($examaudio_id);

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

$smarty->assign("exam_audio" , $exam_audio);
$smarty->assign("bilan"      , $bilan);

$smarty->display("inc_exam_audio/inc_examaudio_bilan.tpl");
