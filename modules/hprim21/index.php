<?php

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_patients"               , null, TAB_READ);
$module->registerTab("pat_hprim_selector"        , null, TAB_READ);
$module->registerTab("httpreq_read_hprim_files"  , null, TAB_READ);
$module->registerTab("httpreq_link_hprim_objects", null, TAB_READ);

?>