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

// Récupération des catégories
$DMICategory = new CDMICategory;
$DMICategory->group_id = $g;
$order = "text";
$DMICategories = $DMICategory->loadMatchingList();

// Chargement du DMI selectionné
$dmi = new CDMI;
$dmi->load(mbGetValueFromGetOrSession("dmi_id"));

// Chargement de tous les dmis
$dmis = $dmi->loadList(null, "nom");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dmis", $dmis);
$smarty->assign("dmi", $dmi);
$smarty->assign("DMICategories",$DMICategories);
$smarty->display("vw_elements.tpl");


?>