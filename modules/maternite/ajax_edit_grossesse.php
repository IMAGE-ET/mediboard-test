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

$user = CMediusers::get();
$user->isProfessionnelDeSante();



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

// sejour & last grossesse
$sejour_id = CValue::get("sejour_id");
$sejour = new CSejour();
$sejour->load($sejour_id);
$grossesse->_ref_sejour = $sejour;

if ($operation = $grossesse->loadRefLastOperation()) {
  $grossesse->_semaine_grossesse = ceil(CMbDT::daysRelative($grossesse->_date_fecondation, CMbDT::date($operation->_datetime)) / 7);
  $grossesse->_terme_vs_operation = CMbDT::daysRelative($grossesse->terme_prevu, CMbDT::date($operation->_datetime));
}

$listPrat = CConsultation::loadPraticiens(PERM_EDIT);

$smarty = new CSmartyDP();
$smarty->assign("grossesse"     , $grossesse);
$smarty->assign("with_buttons"  , $with_buttons);
$smarty->assign("prats"         , $listPrat);
$smarty->assign("user"          , $user);
$smarty->assign("standalone"    , $standalone);
$smarty->display("inc_edit_grossesse.tpl");
