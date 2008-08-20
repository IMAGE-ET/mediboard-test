<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Fabien Ménager
*/
/*
if (isset($_POST['datetime']) && ($_POST['datetime'] == 'now')) {
  $_POST['datetime'] = mbDateTime();
}
*/
$do = new CDoObjectAddEdit('CConstantesMedicales', 'constantes_medicales_id');
$do->doIt();

?>