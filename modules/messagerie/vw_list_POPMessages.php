<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CCanDo::checkAdmin();

$user = CMediusers::get();

//get the sources
//POP
$log_pop = new CSourcePOP();
$log_pop->name = "user-pop-".$user->_id;
$log_pop->loadMatchingObject();

//list for each catergory
$newMsgL = array();
$receivedMsg = array();
$msgSent = array();
$msgBin = array();


$AcountSet = isset($log_pop->_id);

if ($AcountSet) {
  $pop = new CPop($log_pop);
  $pop->open();
  //$newMsg = $log_pop->mailboxSearch("SINCE 02-Dec-2012",true);
  $unseen           = $pop->search('UNSEEN');
  $received         = $pop->search('TO "'.$log_pop->user.'" ');
  $sent             = $pop->search('FROM "'.$log_pop->user.'" ');
  $bin              = $pop->search('DELETED');

  if(is_array($unseen)){
    foreach($unseen as $_mail) {
      $mail_unseen = new CUserMail();
      if( $mail_unseen->loadHeaderFromSource($pop->header($_mail)) ){
        $newMsgL[] = $mail_unseen;
      }
      //$mail->content = $log_pop->mailboxGetpart($_mail);
    }
  }

  if(is_array($received)){
    foreach($received as $_mail) {
      $mail_received = new CUserMail();
      if ($mail_received->loadHeaderFromSource($pop->header($_mail))){
        $receivedMsg[] = $mail_received;
      }
    }
  }

  if(is_array($sent)){
    foreach($sent as $_mail3) {
      $mail_sent = new CUserMail();
      $mail_sent->loadHeaderFromSource($pop->header($_mail3));
      //$mail->content = $log_pop->mailboxGetpart($_mail);
      $msgSent[] = $mail_sent;
    }
  }


  if(is_array($bin)){
    foreach($bin as $_mail4) {
      $mail = new CUserMail();
      $mail->loadHeaderFromSource($pop->header($_mail4));
      //$mail->content = $log_pop->mailboxGetpart($_mail);
      $msgBin[] = $mail;
    }
  }


  $pop->close();
}

$listMails = array(
  "unseen"      =>$newMsgL,
  "inbox"       =>$receivedMsg,
  "sent"        =>$msgSent,
  "bin"         =>$msgBin
);

$smarty = new CSmartyDP();
$smarty->assign("listMails", $listMails);
$smarty->assign("account_ok",   $AcountSet);
$smarty->display("vw_POP_messages.tpl");




//get the mails