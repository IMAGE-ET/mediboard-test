<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// Sets the values to the session too
mbGetAbsValueFromPostOrSession("_conduction");
mbGetAbsValueFromPostOrSession("_oreille");

$do = new CDoObjectAddEdit("CExamAudio", "examaudio_id");
$do->doIt();
?>
