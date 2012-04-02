<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$grossesse_id      = CValue::get('grossesse_id');
$parturiente_id    = CValue::getOrSession("parturiente_id");

$grossesse = new CGrossesse;
$grossesse->load($grossesse_id);

if (!$grossesse->_id) {
  $grossesse->parturiente_id = $parturiente_id;
}

$smarty = new CSmartyDP;

$smarty->assign("grossesse" , $grossesse);

$smarty->display("inc_edit_grossesse.tpl");

?>