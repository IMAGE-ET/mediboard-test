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
$hierarchie = CHierarchieCsARR::get($code);
$hierarchie->loadRefsNotesHierarchies();
$hierarchie->loadRefsParentHierarchies();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hierarchie", $hierarchie);

$smarty->display("vw_hierarchie_csarr.tpl");

?>