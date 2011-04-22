<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//recuperer avec get la variable $facturecatalogueitem_id
$facturecatalogueitem_id = CValue::getOrSession("facturecatalogueitem_id");

$catalogue_item = new CFacturecatalogueitem;
$catalogue_list = $catalogue_item->loadList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("catalogue_list", $catalogue_list);
$smarty->display("inc_list_cataloguefacture.tpl");

?>