<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_class = CValue::get("object_class");
$object_id    = CValue::get("object_id");

$modele_etiquette = new CModeleEtiquette();
$modele_etiquette->object_class = $object_class;
$modele_etiquette->group_id = CGroups::loadCurrent()->_id;

$modeles_etiquettes = $modele_etiquette->loadMatchingList();

$smarty = new CSmartyDP();

$smarty->assign("modeles_etiquettes", $modeles_etiquettes);
$smarty->assign("object_class", $object_class);
$smarty->assign("object_id", $object_id);

$smarty->display("inc_choose_modele_etiquette.tpl");
