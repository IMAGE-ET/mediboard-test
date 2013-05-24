<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$hprim21_source = CExchangeSource::get("hprim21", "ftp", true);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("hprim21_source" , $hprim21_source);
$smarty->display("configure.tpl");

