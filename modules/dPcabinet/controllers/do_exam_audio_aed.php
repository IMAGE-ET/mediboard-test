<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// Sets the values to the session too
CValue::postOrSessionAbs("_conduction");
CValue::postOrSessionAbs("_oreille");

$do = new CDoObjectAddEdit("CExamAudio", "examaudio_id");
$do->doIt();
?>
