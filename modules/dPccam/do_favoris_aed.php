<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

$do = new CDoObjectAddEdit("CFavoriCCAM", "favoris_id");
$do->redirect = null;
$do->doIt();

?>