<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CCanDo::checkRead();

$usermessage_id = CValue::get('usermessage_id');
$user = CMediusers::get();

$usermessage = new CUserMessage();
$usermessage->load($usermessage_id);

if ($usermessage->_id) {
  $usermessage->loadRefDests();
  $usermessage->loadRefDestUser();
  $usermessage->loadRefCreator()->loadRefFunction();
  foreach ($usermessage->_ref_destinataires as $_destinataire) {
    $_destinataire->loadRefTo()->loadRefFunction();
  }

  $mode = 'sentbox';
  if ($usermessage->_ref_dest_user->_id) {
    if ($usermessage->_ref_dest_user->archived) {
      $mode = 'archive';
    }
    else {
      $mode = 'inbox';
    }

    if (!$usermessage->_ref_dest_user->datetime_read) {
      $usermessage->_ref_dest_user->datetime_read = CMbDT::dateTime();
      $usermessage->_ref_dest_user->store();
    }
  }

  if ($mode == 'sentbox') {
    $usermessage->_ref_dest_user = reset($usermessage->_ref_destinataires);
  }

  $smarty = new CSmartyDP();
  $smarty->assign('usermessage', $usermessage);
  $smarty->assign('mode', $mode);
  $smarty->display('inc_view_usermessage.tpl');
}