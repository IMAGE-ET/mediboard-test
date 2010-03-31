<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7816 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("vw_optimization_echange_hprim.tpl");
?>