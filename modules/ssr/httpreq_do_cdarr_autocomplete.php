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

$needle = CValue::post("code_activite_cdarr", CValue::post("code_cdarr", CValue::post("code")));
if (!$needle) {
  $needle = "%";
}

$activite = new CActiviteCdARR();
/** @var CActiviteCdARR[] $activites */
$activites = $activite->seek($needle, null, 300);
foreach ($activites as $_activite) {
  $_activite->loadRefTypeActivite();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("activites", $activites);
$smarty->assign("needle"   , $needle);
$smarty->assign("nodebug"  , true);

$smarty->display("inc_do_cdarr_autocomplete.tpl");
