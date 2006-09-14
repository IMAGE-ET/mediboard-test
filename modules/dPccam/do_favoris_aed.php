<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

$do = new CDoObjectAddEdit("CFavoriCCAM", "favoris_id");
$do->createMsg = "Favori créé";
$do->modifyMsg = "Favori modifié";
$do->deleteMsg = "Favori supprimé";
$do->redirect = null;
$do->doIt();

?>