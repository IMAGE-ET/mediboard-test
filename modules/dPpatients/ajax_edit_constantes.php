<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CCanDo::checkRead();

$constant_id = CValue::get('constant_id');

$constant = new CConstantesMedicales();
$constant->load($constant_id);
$constant->loadRefContext();
$constant->loadRefPatient();
$constant->updateFormFields();

$smarty = new CSmartyDP();
$smarty->assign('constant', $constant);
$smarty->display('inc_edit_constant.tpl');