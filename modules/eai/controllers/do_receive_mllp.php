<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage eai
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$port    = CValue::post("port");
$message = CValue::post("message");

mbLog($message, "PORT $port");
