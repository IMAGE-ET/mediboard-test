<?php /* $Id: do_message_aed.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

unlink("tmp/mb-log.html");

$AppUI->redirect();

?>