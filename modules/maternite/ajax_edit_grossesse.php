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

// vars
$grossesse_id   = CValue::get('grossesse_id');
$parturiente_id = CValue::getOrSession("parturiente_id");

// options
$with_buttons   = CValue::get("with_buttons", false); // see buttons at the right panel
$standalone     = CValue::get("standalone", 0);       // embedded in a requestUpdate for example

$grossesse = new CGrossesse();
$grossesse->load($grossesse_id);
$grossesse->loadRefsNotes();

if (!$grossesse->_id) {
  $grossesse->parturiente_id = $parturiente_id;
}
$grossesse->loadRefParturiente();

$listPrat = CConsultation::loadPraticiens(PERM_EDIT);

$smarty = new CSmartyDP();
$smarty->assign("grossesse" , $grossesse);
$smarty->assign("with_buttons", $with_buttons);
$smarty->assign("prats", $listPrat);
$smarty->assign("standalone", $standalone);
$smarty->display("inc_edit_grossesse.tpl");
