<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$id400 = new CIdSante400();
$id400->object_class = "CGroups";

// Find groups
$groups = array();
$groups_ids = array();
$id400->tag = "eCap";
foreach ($id400->loadMatchingList() as $_id400) {
  $_id400->loadTargetObject();
	$group = $_id400->_ref_object;
	$groups[$group->_id] = $group;
	$groups_ids[$group->_id] = array(
	 "eCap" => $_id400,
	);
}

// Groups tags (true meaning editable)
$groups_tags = array(
  "eCap" => false,
  "eCap SHS" => false,
	"eCap URGSER" => true,
);

// Find other ids for groups
foreach ($groups as $_group) {
	foreach ($groups_tags as $_tag => $_editable) {
	  $id400 = new CIdSante400();
	  $id400->loadLatestFor($_group, $_tag);
    $groups_ids[$group->_id][$_tag] = $id400;
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("groups", $groups);
$smarty->assign("groups_ids", $groups_ids);
$smarty->assign("groups_tags", $groups_tags);

$smarty->display("vw_identifiants.tpl");
?>