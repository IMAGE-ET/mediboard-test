<?php /* $Id: httpreq_do_element_autocomplete.php 8169 2010-03-02 15:31:33Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 8169 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$needle = CValue::post("code_activite_csarr", CValue::post("code"));
if (!$needle) {
	$needle = "%";
}

$activite = new CActiviteCsARR();
$activites = $activite->seek($needle, null, 300);
foreach ($activites as $_activite) {
  $_activite->loadRefHierarchie();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("activites", $activites);
$smarty->assign("needle"   , $needle);
$smarty->assign("nodebug"  , true);

$smarty->display("inc_do_csarr_autocomplete.tpl");

?>