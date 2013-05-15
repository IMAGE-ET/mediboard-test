<?php 

/**
 * Ajout de personnel et changement de salle
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */
 
$op_id = CValue::get("op_id");

$operation = new COperation();
$operation->load($op_id);

$operation->loadRefPlageOp();
$operation->loadAffectationsPersonnel();

$blocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

$smarty = new CSmartyDP();

$smarty->assign("operation", $operation);
$smarty->assign("blocs"    , $blocs);

$smarty->display("inc_edit_extra_interv.tpl");