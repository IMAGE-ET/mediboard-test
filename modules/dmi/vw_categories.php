<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Thomas Despoix
 */

global $can;
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("vw_categories.tpl");

?>