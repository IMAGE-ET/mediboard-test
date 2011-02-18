<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$rpu_id = CValue::get("rpu_id");

$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejour();

$sejour = $rpu->_ref_sejour;
$sejour->_ref_rpu = $rpu;
$sejour->loadRefPatient()->loadIPP();
$sejour->loadNumDossier();
$sejour->loadRefsConsultations();

// Praticiens urgentistes
$group = CGroups::loadCurrent();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rpu", $rpu);
$smarty->assign("sejour", $sejour);
$smarty->assign("userSel"  , CAppUI::$user);

$smarty->display("inc_edit_sortie.tpl");
?>