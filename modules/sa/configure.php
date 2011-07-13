<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sms
 * @version $Revision: 10467 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>