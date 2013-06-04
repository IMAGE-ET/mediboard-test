<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$acte = new CActeNGAP;
$acte->quantite    = CValue::get("quantite", "1");
$acte->code        = CValue::get("code");
$acte->coefficient = CValue::get("coefficient", "1");
$acte->demi        = CValue::get("demi");
$acte->complement  = CValue::get("complement");
$acte->updateMontantBase();
$acte->getLibelle();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("acte"  , $acte);
$smarty->display("inc_vw_tarif_ngap.tpl");


?>