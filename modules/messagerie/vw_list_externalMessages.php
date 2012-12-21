<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CCanDo::checkRead();
$user = CUser::get();

//unseen
$mail = new CUserMail();
$mail->user_id = $user->_id;

$where = array();
$order = "date_inbox DESC";

$mails_all = $mail->countMatchingList($order);


$mails = array();
$mails["all"]     = $mails_all;


//smarty
$smarty = new CSmartyDP();
$smarty->assign("user",  $user);
$smarty->assign("mails", $mails);
$smarty->display("vw_list_externalMessages.tpl");