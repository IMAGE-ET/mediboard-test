<?php

/**
 * Modification de grossesse
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$grossesse_id   = CValue::get('grossesse_id');
$parturiente_id = CValue::getOrSession("parturiente_id");
$with_buttons = CValue::get("with_buttons", false);

$grossesse = new CGrossesse();
$grossesse->load($grossesse_id);
$grossesse->loadRefsNotes();

if (!$grossesse->_id) {
  $grossesse->parturiente_id = $parturiente_id;
}
$grossesse->loadRefParturiente();

$smarty = new CSmartyDP();
$smarty->assign("grossesse" , $grossesse);
$smarty->assign("with_buttons", $with_buttons);
$smarty->display("inc_edit_grossesse.tpl");
