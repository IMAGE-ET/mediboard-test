<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
CCanDo::checkEdit();
$facture_class  = CValue::get("facture_class", "CFactureCabinet");
$facture_id     = CValue::get("facture_id");

$facture = new $facture_class;
$facture->load($facture_id);

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("facture",  $facture);

$smarty->display("vw_edit_definitive.tpl");
