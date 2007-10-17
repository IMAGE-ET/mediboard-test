<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$text = mbGetValueFromGet("text");

echo $AppUI->_($text);

?>