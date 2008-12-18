<?php /* $Id: do_exam_audio_aed.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Romain Ollivier
*/

// Sets the values to the session too
mbGetAbsValueFromPostOrSession("_conduction");
mbGetAbsValueFromPostOrSession("_oreille");

$do = new CDoObjectAddEdit("CExamAudio", "examaudio_id");
$do->doIt();
?>
