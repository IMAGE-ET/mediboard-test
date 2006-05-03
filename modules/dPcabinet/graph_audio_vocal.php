<?php /* $Id: graph_audio_vocal.php,v 1.2 2005/11/30 17:05:26 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.2 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "examaudio"));

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
$where["consultation_id"] = "= '$consultation_id'";
$exam_audio = new CExamAudio;
$exam_audio->loadObject($where);

require_once( $AppUI->getModuleFile("$m", "inc_graph_audio_vocal"));
$graph_vocal->Stroke();