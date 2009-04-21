<?php

/**
* @package Mediboard
* @subpackage dPpersonnel
* @version $Revision$
* @author Alexis Granger
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_affectations_pers", null, TAB_READ);
$module->registerTab("vw_edit_personnel", null, TAB_READ);
?>