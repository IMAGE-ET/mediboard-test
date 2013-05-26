<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CValue::get("module");
$name = CValue::get("name");
$value = CValue::get("value");

// Ajout de la valeur en session
$_SESSION[$module][$name] = $value;
