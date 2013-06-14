<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */


CCanDo::checkRead();
$mail_id = CValue::get("mail_id", 0);
$pat_id  = CValue::get("pat_id");

//apicrypt & search
$patient = new CPatient();
$dossier = new CSejour();

//looking for apicrypt => patient
$mail= new CUserMail();
$mail->load($mail_id);
$mail->loadContentPlain();

//apicrypt case
if (stripos($mail->_text_plain->content, "[apicrypt]") !== false) {
  $lines = explode("\n", $mail->_text_plain->content);
  mbLog($lines);

  //cleanup line 1 to 13
  for ($a = 1; $a<13; $a++) {
    $lines[$a] = trim($lines[$a]);
  }

  //IPP
  if ($lines[1] != '') {
    $patient->_IPP = $lines[1];
    $patient->loadFromIPP();
  }

  //search
  if (!$patient->_id && $lines[2] != '' && $lines[3] != "") {
    $lines[7] = CMbDT::dateFromLocale($lines[7]);

    $where = array();
    $where[]            = "`nom` LIKE '$lines[2]%' OR `nom_jeune_fille` LIKE '$lines[2]%'";
    $where["prenom"]    = "LIKE '$lines[3]%' ";
    $where["naissance"] = "LIKE '$lines[7]' ";

    $patient->loadObject($where);
  }

  //NDA
  if ($patient->_id && $lines[9]) {
    $dossier->loadFromNDA($lines[9]);
  }

  // patient + date
  if ($patient->_id && !$dossier->_id && $lines[10]) {
    $lines[10] = CMbDT::dateTime(CMbDT::dateFromLocale($lines[10]));

    $where = array();
    $where[]             = " '$lines[10]' BETWEEN entree AND sortie ";
    $where["patient_id"] = " = '$patient->_id'";

    $dossier->loadObject($where);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("mail_id",    $mail_id);
$smarty->assign("patient",    $patient);
$smarty->assign("dossier_id", $dossier->_id);

$smarty->display("inc_vw_attach_piece.tpl");
