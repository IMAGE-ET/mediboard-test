<?php /* $Id: configure.php 15776 2012-06-05 07:54:52Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 15776 $
 * @author Romain Ollivier
 */

CCanDo::Admin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");