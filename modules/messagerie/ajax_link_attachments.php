<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */


CCanDo::checkRead();
$mail_id = CValue::get("mail_id", 0);
$pat_id = CValue::get("pat_id");

$pat = new CPatient();
$pat->load($pat_id);

//smarty
$smarty = new CSmartyDP();
$smarty->assign("mail_id", $mail_id);
$smarty->assign("pat", $pat);
$smarty->display("inc_vw_attach_piece.tpl");