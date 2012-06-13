<?php

/**
 * Installation error log
 *
 * PHP version 5.1.x+
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id: checkauth.php 15808 2012-06-10 18:19:07Z mytto $ 
 * @link       http://www.mediboard.org
 */

require_once "checkauth.php";

showHeader();

// Escape if the file doesn't exist
@readfile("../tmp/mb-log.html");

require "valid.php";

showFooter(); ?>
