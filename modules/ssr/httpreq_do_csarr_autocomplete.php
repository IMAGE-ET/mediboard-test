<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$needle = CValue::post("code_activite_csarr", CValue::post("code_csarr", CValue::post("code")));
if (!$needle) {
	$needle = "%";
}

$activite = new CActiviteCsARR();
/** @var CActiviteCsARR[] $activites */
$activites = $activite->seek($needle, null, 300);
foreach ($activites as $_activite) {
  $_activite->loadRefHierarchie();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("activites", $activites);
$smarty->assign("needle"   , $needle);
$smarty->assign("nodebug"  , true);

$smarty->display("inc_do_csarr_autocomplete.tpl");

?>