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

CCando::checkRead();

$account_id = Cvalue::get('account_id');

$counts = array(
  array('name' => 'inbox', 'count' =>  CUserMail::countUnread($account_id)),
  array('name' => 'archived', 'count' =>  CUserMail::countArchived($account_id)),
  array('name' => 'sentbox', 'count' =>  CUserMail::countSent($account_id)),
  array('name' => 'favorites', 'count' =>  CUserMail::countFavorites($account_id)),
  array('name' => 'drafts', 'count' => CUserMail::countDrafted($account_id))
);

echo json_encode($counts);