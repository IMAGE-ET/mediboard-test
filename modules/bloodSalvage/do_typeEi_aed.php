<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision:  $
* @author Alexandre Germonneau
*/

$do = new CDoObjectAddEdit('CTypeEi', 'type_ei_id');

$do->modifyMsg = "Modèle de fiche modifié";
$do->createMsg = "Modèle de fiche créé";
$do->doIt();

?>