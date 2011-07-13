<?php 

/**
 * Configure
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$object_servers = array(
  "sip" => "CSipObjectHandler",
  "smp" => "CSmpObjectHandler",
  "sms" => "CSmsObjectHandler",
  "sa"  => "CSaObjectHandler",
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object_servers", $object_servers);
$smarty->display("configure.tpl");

?>