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
$activite->loadRefsNotesActivites();
$activite->loadRefsModulateurs();
$activite->loadRefsGestesComplementaires();
$activite->loadRefsHierarchies();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("activite"      , $activite);

$smarty->display("vw_code_csarr.tpl");

?>