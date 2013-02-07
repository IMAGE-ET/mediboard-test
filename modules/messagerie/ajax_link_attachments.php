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

//apicrypt & search
$patient = new CPatient();

//looking for apicrypt => patient
$mail= new CUserMail();
$mail->load($mail_id);
$mail->loadContentPlain();
$mail->loadContentHTML();
if (stripos($mail->_text_plain->content, "[apicrypt]") !== false) {
  $lines = explode("\n", $mail->_text_plain->content);
  if ($lines[1] != '') {
    $patient->_IPP = trim($lines[1]);
    $patient->loadFromIPP();

    if (!$patient->_id && $lines[2] != '' && $lines[3] != "") {
      $where = array();
      $where[] = "`nom` LIKE '$lines[2]%' OR `nom_jeune_fille` LIKE '$lines[2]%'";
      $where["prenom"] = "LIKE '$lines[3]%'";
      $patient->loadObject($where);
    }
  }
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("mail_id", $mail_id);
$smarty->assign("patient", $patient);
$smarty->display("inc_vw_attach_piece.tpl");