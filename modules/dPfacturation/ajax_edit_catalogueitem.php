<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//recuperer avec get la variable $facturecatalogueitem_id
$facturecatalogueitem_id = CValue::getOrSession("catalogue_item");
$catalogue_item=new CFacturecatalogueitem;
$catalogue_item->load($facturecatalogueitem_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("catalogue_item",$catalogue_item);
$smarty->display("inc_edit_cataloguefacture.tpl");

?>