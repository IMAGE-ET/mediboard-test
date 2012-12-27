<?php /* $Id: do_usermessage_aed.php 17062 2012-10-25 10:32:26Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision: 17062 $
 * @author Thomas despoix
 */

$do = new CDoObjectAddEdit("CUserMail", "user_mail_id");
$do->doIt();

?>