<?php /* $Id: do_plageressource_aed.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPsante400
* @version $Revision: 23 $
* @author Thomas Despoix
**/

global $AppUI;

$do = new CDoObjectAddEdit("CIdSante400", "id_sante400_id");
$do->createMsg = "ID Santé 400 créé";
$do->modifyMsg = "ID Santé 400 modifié";
$do->deleteMsg = "ID Santé 400 supprimé";
$do->redirect = null;
$do->doIt();


?>