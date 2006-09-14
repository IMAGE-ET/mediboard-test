<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
$where["consultation_id"] = "= '$consultation_id'";
$exam_audio = new CExamAudio;
$exam_audio->loadObject($where);

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_vocal"));
$graph_vocal->Stroke();