<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage todo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$smarty = new CSmartyDP();

//$smarty->assign('var', $states);

$smarty->display("configure.tpl");
