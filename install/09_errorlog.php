<?php

/**
 * Installation error log
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkauth.php";

showHeader();

// Disable output buffer to be able to see big error logs
ob_end_clean();

// Escape if the file doesn't exist
@readfile("../tmp/mb-log.html");

showFooter();
