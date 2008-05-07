<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Fabien Ménager
*/

if (isset($_POST['date']) && $_POST['date'] == 'now') {
  $_POST['date'] = mbDateTime();
}

$do = new CDoObjectAddEdit('CReglement', 'reglement_id');

$do->doIt();
?>