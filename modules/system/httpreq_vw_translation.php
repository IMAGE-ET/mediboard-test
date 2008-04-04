<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

$text = mbGetValueFromGet("text");
echo CAppUI::tr($text);

?>