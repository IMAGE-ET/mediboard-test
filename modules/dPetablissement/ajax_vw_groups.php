<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Récupération du groupe selectionné
$group = new CGroups;
$group->load(CValue::getOrSession("group_id"));

if ($group && $group->_id) {
  $group->loadFunctions();
  $group->loadRefsNotes();
  $group->loadRefLegalEntity();
}

$legal_entity = new CLegalEntity();
$legal_entities = $legal_entity->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("group"          , $group);
$smarty->assign("legal_entities" , $legal_entities);

$smarty->display("inc_vw_groups.tpl");
