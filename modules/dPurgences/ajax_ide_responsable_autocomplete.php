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

$where = array(
  "actif"       => "= '1'",
  "function_id" => "= '$group->service_urgences_id'",
  "user_type"   => "= '7'",
);

$leftjoin = array(
  "users" => "users_mediboard.user_id = users.user_id",
);

$mediuser = new CMediusers();
//Suppression du seekable sur les fonctions, on recherche sur une fonction en particulier
unset($mediuser->_specs["function_id"]->seekable);
/** @var CMediusers[] $matches */

$matches = $mediuser->seek($keywords, $where, 50, null, $leftjoin, "user_last_name");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("keywords", $keywords);
$smarty->assign("matches" , $matches);

$smarty->display("inc_autocomplete_ide_responsable.tpl");