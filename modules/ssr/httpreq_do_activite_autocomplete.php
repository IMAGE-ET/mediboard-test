<?php /* $Id: httpreq_do_element_autocomplete.php 8169 2010-03-02 15:31:33Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 8169 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$needle = CValue::post("code_activite_cdarr", CValue::post("code","aaa"));
$activite = new CActiviteCdARR();

$activites = $activite->seek($needle);
foreach($activites as $_activite) {
  $_activite->loadRefTypeActivite();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("activites", $activites);
$smarty->assign("needle"   , $needle);
$smarty->assign("nodebug"  , true);

$smarty->display("inc_do_activite_autocomplete.tpl");

?>