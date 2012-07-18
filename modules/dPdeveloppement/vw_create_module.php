<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
 
CCanDo::checkAdmin();

$licenses = array(
  "GNU GPL" => "GNU General Public License, see http://www.gnu.org/licenses/gpl.html",
  "OXOL"    => "OXOL, see http://www.mediboard.org/public/OXOL",
);

$smarty = new CSmartyDP();

$smarty->assign("licenses", $licenses);

$smarty->display("vw_create_module.tpl");
    