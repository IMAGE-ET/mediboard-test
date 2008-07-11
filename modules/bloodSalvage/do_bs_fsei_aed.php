<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision:  $
* @author Alexandre Germonneau
*/


global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CFicheEi", "fiche_ei_id");

$do->doStore();
$do->doIt();
?>