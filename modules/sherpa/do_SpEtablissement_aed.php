<?php

/**
 *  @package Mediboard
 *  @subpackage sherpa
 *  @version $Revision: $
 *  @author 
 */

global $AppUI;

$do = new CDoObjectAddEdit("CSpEtablissement", "sp_etab_id");
$do->doIt();

?>