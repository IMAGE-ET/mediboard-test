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

CCanDo::check();

// Creation d'une banque
$banque = new CBanque();
$banque_id = CValue::getOrSession("banque_id");

$order = "nom ASC";
$banques = $banque->loadList(null, $order);

// Chargement de la banque selectionnée
if ($banque_id) {
  $banque->load($banque_id);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banque"    , $banque);
$smarty->assign("banques"   , $banques);

$smarty->display("vw_banques.tpl");
