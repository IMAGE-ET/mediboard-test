<?php 

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$account_id = CValue::get("account_id");

$account = new CSourcePOP();
$account->load($account_id);
if ($account_id) {
  CValue::setSession("account_id", $account_id);
}

$mail = new CUserMail();
$whereGlob = array();
$whereGlob["account_id"] = " = '$account->_id'";

$where = array();
$where["sent"] = "= '1'";
$nbSent = $mail->countList(array_merge($where, $whereGlob));

$where = array();
$where["favorite"] = " = '1'";
$nbFavorite = $mail->countList(array_merge($where, $whereGlob));

$where = array();
$where["archived"] = " = '1'";
$nbArchived = $mail->countList(array_merge($where, $whereGlob));

$where = array();
$where["date_read"] = " IS NULL";
$nbUnseen = $mail->countList(array_merge($where, $whereGlob));

$where = array();
$where["archived"] = " = '0'";
$where["sent"] = " = '0'";
$nbTotal = $mail->countList(array_merge($where, $whereGlob));

//smarty
$smarty = new CSmartyDP();
$smarty->assign("account", $account);
$smarty->assign("nbTotal", $nbTotal);
$smarty->assign("nbUnseen", $nbUnseen);
$smarty->assign("nbArchived", $nbArchived);
$smarty->assign("nbFavorite", $nbFavorite);
$smarty->assign("nbSent", $nbSent);
$smarty->display("vw_account_mail.tpl");