<?php /* $Id: do_service_aed.php 8216 2010-03-05 10:16:33Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 8216 $
* @author Thomas Despoix
*/

$do = new CDoObjectAddEdit("CUniteFonctionnelle", "uf_id");
$do->doIt();
?>