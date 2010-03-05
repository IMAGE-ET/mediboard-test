<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

$do = new CDoObjectAddEdit("CExamPossum", "exampossum_id");
$do->redirect = null;
$do->doIt();
?>