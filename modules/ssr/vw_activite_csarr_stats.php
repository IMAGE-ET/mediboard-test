<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$code = CValue::get("code");
$activite = CActiviteCsARR::get($code);
if (!$activite->code) {
  CAppUI::stepMessage(UI_MSG_ERROR, "Activité CsARR '$code' non trouvée");
  return;
}

$activite->loadRefsElementsByCat();
$activite->loadRefsAllExecutants();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("activite", $activite);

$smarty->display("vw_activite_srr_stats.tpl");

?>