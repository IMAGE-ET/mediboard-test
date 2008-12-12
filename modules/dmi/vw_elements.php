<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Stéphanie Subilia
 */

global $can, $g;
$can->needsRead();

// Récupération des catégories
$DMICategory = new CDMICategory;
$DMICategory->group_id = $g;
$order = "text";
$DMICategories = $DMICategory->loadMatchingList();

// Chargement du DMI selectionné
$dmi_id = mbGetValueFromGetOrSession("dmi_id");
$dmi = new CDMI;
$dmi->category_id = mbGetValueFromGet("category_id");
$dmi->load($dmi_id);

// Chargement de tous les dmis
foreach ($DMICategories as &$_category) {
  $_category->loadRefsDMI();
    foreach ($_category->_ref_dmis as &$_dmi)
    {
    	$_dmi->loadRefProduit();
    	$_dmi->_ref_product->loadRefsFwd();
    }
}

// Vérification du groupe courant pour le DMI sélectionné
$category_dmi = new CDMICategory;
$category_dmi->load($dmi->category_id);
if ($category_dmi->group_id != $g) {
  $dmi = new CDMI;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dmi", $dmi);
$smarty->assign("dmi_id", $dmi_id);
$smarty->assign("DMICategories",$DMICategories);
$smarty->display("vw_elements.tpl");


?>