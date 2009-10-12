<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien Ménager
*/

global $can;
$can->needsRead();


// Création du template
$smarty = new CSmartyDP();
$smarty->display('css_test.tpl');

?>