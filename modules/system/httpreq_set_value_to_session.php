<?php

/**
* @package Mediboard
* @subpackage system
* @version $Revision:
* @author Alexis Granger
*/

$module = mbGetValueFromGet("module");
$name = mbGetValueFromGet("name");
$value = mbGetValueFromGet("value");

// Ajout de la valeur en session
$_SESSION[$module][$name] = $value;

?>