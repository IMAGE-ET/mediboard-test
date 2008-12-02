<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Thomas Despoix
 */

global $can, $g;
$can->needsRead();
$ds = CSQLDataSource::get("std");

// Récupération des catégories
$DMICategory = new CDMICategory;
$DMICategory->group_id = $g;
$order = "text";
$DMICategories = $DMICategory->loadMatchingList();

// Chargement du DMI selectionné
$dmi = new CDMI;
$where[] = "`category_id` ".$ds->prepareIn(array_keys($DMICategories));
$dmi->load(mbGetValueFromGetOrSession("dmi_id"));

// Chargement de tous les dmis
$dmis = $dmi->loadList($where, "nom");

$category_dmi = new CDMICategory;
$category_dmi->load($dmi->category_id);
if($category_dmi->group_id != $g)
  $dmi=new CDMI;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dmis", $dmis);
$smarty->assign("dmi", $dmi);
$smarty->assign("DMICategories",$DMICategories);
$smarty->display("vw_elements.tpl");


?>