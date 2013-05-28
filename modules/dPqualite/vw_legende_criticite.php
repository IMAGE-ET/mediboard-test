<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
CCanDo::checkRead();

$smarty = new CSmartyDP();
$smarty->assign("fiche", new CFicheEi);
$smarty->display("vw_legende_criticite.tpl"); 
