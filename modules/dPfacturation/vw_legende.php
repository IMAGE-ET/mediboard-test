<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
CCanDo::checkRead();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("classe", CValue::get("classe"));

$smarty->display("vw_legende.tpl");
?>
