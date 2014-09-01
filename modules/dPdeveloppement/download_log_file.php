<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

ob_end_clean();

header("Content-Type: application/html");
header("Content-Length: ".filesize(LOG_PATH));
header("Content-Disposition: attachment; filename=mb-log.html");

readfile(LOG_PATH);

CApp::rip();