<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkAdmin();

ob_end_clean();

header("Content-Type: application/html");
header("Content-Length: ".filesize(LOG_PATH));
header("Content-Disposition: attachment; filename=\"mb-log.".CMbDT::dateTime().".html\"");

readfile(LOG_PATH);

CApp::rip();