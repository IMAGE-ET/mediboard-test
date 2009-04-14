<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = mbGetValueFromGet("module");
$name = mbGetValueFromGet("name");
$value = mbGetValueFromGet("value");

// Ajout de la valeur en session
$_SESSION[$module][$name] = $value;

?>