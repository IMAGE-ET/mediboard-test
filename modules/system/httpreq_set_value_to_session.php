<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CValue::get("module");
$name = CValue::get("name");
$value = CValue::get("value");

// Ajout de la valeur en session
$_SESSION[$module][$name] = $value;

?>