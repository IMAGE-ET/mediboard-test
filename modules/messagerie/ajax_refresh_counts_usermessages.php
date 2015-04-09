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

$user = CMediusers::get();

$counts = array(
  array('name' => 'inbox', 'count' =>  CUserMessageDest::countUnreadFor($user)),
  array('name' => 'archive', 'count' =>  CUserMessageDest::countArchivedFor($user)),
  array('name' => 'sentbox', 'count' =>  CUserMessageDest::countSentFor($user)),
  array('name' => 'draft', 'count' =>  CUserMessageDest::countDraftedFor($user))
);

echo json_encode($counts);