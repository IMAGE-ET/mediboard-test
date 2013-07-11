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
$debiteur_id = CValue::get("debiteur_id");

$debiteur = new CDebiteur();
$debiteur->load($debiteur_id);

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("debiteur",  $debiteur);
$smarty->assign("debiteur_dec",  CValue::get("debiteur_desc", 0));

$smarty->display("vw_edit_debiteur.tpl");
