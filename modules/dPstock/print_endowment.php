<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$endowment_id = CValue::get('endowment_id');

$endowment = new CProductEndowment();
$endowment->load($endowment_id);
$endowment->loadRefsFwd();
$endowment->updateFormFields();
$endowment->loadRefsBack();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('endowment', $endowment);
$smarty->display('print_endowment.tpl');

