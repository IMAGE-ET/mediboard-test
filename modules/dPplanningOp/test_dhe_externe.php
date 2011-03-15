<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();
$patient      = new CPatient();
$sejour       = new CSejour();
$intervention = new COperation();

$praticien  = new CMediusers();
$praticiens = $praticien->loadPraticiens(PERM_EDIT);
 
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticiens"  , $praticiens);
$smarty->assign("patient"     , $patient);
$smarty->assign("sejour"      , $sejour);
$smarty->assign("intervention", $intervention);

$smarty->display("test_dhe_externe.tpl");

?>
