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
$activite = CActiviteCsARR::get($code);
$activite->loadRefsNotesActivites();
$activite->loadRefsModulateurs();
$activite->loadRefsGestesComplementaires();
$activite->loadRefsHierarchies();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("activite"      , $activite);

$smarty->display("vw_activite_csarr.tpl");
