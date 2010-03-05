<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

$do = new CDoObjectAddEdit("CExamIgs", "examigs_id");
$do->redirect = null;
$do->doIt();
?>