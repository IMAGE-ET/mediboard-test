<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Stéphanie Subilia
 */

global $can, $g;
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("vw_elements.tpl");


?>