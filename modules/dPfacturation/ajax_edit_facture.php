<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$facture_id = CValue::getOrSession("facture_id");

$facture = new CFacture();
$facture->load($facture_id);
$facture->loadRefsBack();
$facture->loadRefsFwd();

$factureitem = new CFactureItem();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture", $facture);
$smarty->assign("factureitem", $factureitem);

$smarty->display("inc_edit_facture.tpl");

?>