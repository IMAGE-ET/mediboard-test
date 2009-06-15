<?php /* $Id: vw_affectations_pers.php 6326 2009-05-19 07:20:26Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 6326 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$_multiple = array();
$object = mbGetObjectFromGet(null, null, "object_guid");

$affectation = new CAffectationPersonnel();
$affectation->setObject($object);
$affectation->personnel_id = mbGetValueFromGet("personnel_id");
$affectation->loadRefPersonnel();

$_multiple["object"]       = $affectation->_ref_object;
$_multiple["personnel"]    = $affectation->_ref_personnel;
$_multiple["affectations"] = $affectation->loadMatchingList();
$_multiple["affect_count"] = count($_multiple["affectations"]);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_multiple", $_multiple);

$smarty->display("inc_affectations_multiple.tpl");
?>
