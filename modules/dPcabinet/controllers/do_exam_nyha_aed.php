<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

$do = new CDoObjectAddEdit("CExamNyha", "examnyha_id");
$do->redirect = null;
$do->doIt();
?>