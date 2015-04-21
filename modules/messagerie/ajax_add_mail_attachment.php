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

$mail_id = CValue::get('mail_id');

$attachment = new CMailAttachments();
$attachment->mail_id = $mail_id;

$smarty = new CSmartyDP();
$smarty->assign('attachment', $attachment);
$smarty->display('inc_add_mail_attachment.tpl');