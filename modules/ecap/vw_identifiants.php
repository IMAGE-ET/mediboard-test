<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$id400 = new CIdSante400();
$id400->object_class = "CGroups";
$id400->tag = "eCap";
$idGroups = $id400->loadMatchingList();
foreach ($idGroups as $idGroup) {
  $idGroup->loadTargetObject();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("idGroups", $idGroups);

$smarty->display("vw_identifiants.tpl");
?>