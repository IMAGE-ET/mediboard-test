<?php /* $Id: do_configure.php 11968 2011-04-22 09:04:52Z mytto $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 11968 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin(); // for now

$data = CValue::post("data");
$data = stripslashes($data);

mbLog(json_decode($data, true));

