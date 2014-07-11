<?php 

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$keywords = CValue::get("ide_responsable_id_view");
$group    = CGroups::loadCurrent();

if ($keywords == "") {
  $keywords = "%%";
}
$mediuser = new CMediusers();
$matches = $mediuser->loadListFromType(array("Infirmière"), PERM_READ, $group->service_urgences_id, $keywords, true, true);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("keywords", $keywords);
$smarty->assign("matches" , $matches);

$smarty->display("inc_autocomplete_ide_responsable.tpl");