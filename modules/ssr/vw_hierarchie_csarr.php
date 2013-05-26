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

$code = CValue::get("code");
$hierarchie = CHierarchieCsARR::get($code);
$hierarchie->loadRefsNotesHierarchies();
$hierarchie->loadRefsParentHierarchies();
$hierarchie->loadRefsChildHierarchies();
$hierarchie->loadRefsActivites();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hierarchie", $hierarchie);

$smarty->display("vw_hierarchie_csarr.tpl");
