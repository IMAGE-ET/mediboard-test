<?php

/**
 * Installation error log
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkauth.php";

showHeader();

// Escape if the file doesn't exist
@readfile("../tmp/mb-log.html");

showFooter(); ?>
