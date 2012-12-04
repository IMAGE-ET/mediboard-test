<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

CCanDo::checkAdmin();

$smarty = new CSmartyDP();

$smarty->display("configure.tpl");