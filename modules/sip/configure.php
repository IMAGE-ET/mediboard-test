<?php 

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron  
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>