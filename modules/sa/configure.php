<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sms
 * @version $Revision: 10467 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$sejour = new CSejour();
$sejour_types = $sejour->_specs["type"]->_list;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour_types", $sejour_types);
$smarty->display("configure.tpl");

?>