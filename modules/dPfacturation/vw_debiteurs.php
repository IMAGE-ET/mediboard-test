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

$debiteur = new CDebiteur();
$debiteurs = $debiteur->loadList(null, "numero");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("debiteurs", $debiteurs);

$smarty->display("vw_debiteurs.tpl");
