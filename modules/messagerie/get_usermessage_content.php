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

$usermessage = new CUserMessage();
$usermessage->load($usermessage_id);

if ($usermessage->_id) {
  echo nl2br($usermessage->content);
}