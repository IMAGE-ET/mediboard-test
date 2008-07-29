<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Fabien Ménager
*/

global $can;
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display('form_tester.tpl');

?>