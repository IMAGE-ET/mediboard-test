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
if ((stripos($mail->_text_plain->content, "[apicrypt]") !== false) || (stripos($mail->_text_plain->content, "*FIN*") !== false)) {
  $lines = explode("\n", $mail->_text_plain->content);
  $fl = ($lines[0] != "[apicrypt]") ? 0 : 1;  //first line


  //cleanup line 1 to 13
  for ($a = $fl; $a<$fl+12; $a++) {
    $lines[$a] = trim($lines[$a]);
  }

  //init
  $ipp        = $lines[$fl];
  $nom        = $lines[$fl+1];
  $prenom     = $lines[$fl+2];
  $addr       = $lines[$fl+3];
  $addr_2     = $lines[$fl+4];
  $cp_ville   = $lines[$fl+5];
  $naissance  = CMbDT::dateFromLocale($lines[$fl+6]);
  $codeSecu   = $lines[$fl+6];
  $nda        = $lines[$fl+8];
  $date       = CMbDT::dateTime(CMbDT::dateFromLocale($lines[$fl+9]));
  $codeCores  = $lines[$fl+10];
  $codePresc  = $lines[$fl+11];

  //IPP
  if ($lines[$fl] != '') {
    $patient->_IPP = $ipp;
    $patient->loadFromIPP();
  }

  //search
  if (!$patient->_id && $nom != '' && $prenom != "") {
    $where = array();
    $where[]            = "`nom` LIKE '$lines[$nom]%' OR `nom_jeune_fille` LIKE '$lines[$nom]%'";
    $where["prenom"]    = "LIKE '$lines[$prenom]%' ";
    $where["naissance"] = "LIKE '$lines[$naissance]' ";
    $patient->loadObject($where);
  }
  mbLog($patient);


  //NDA
  if ($patient->_id && $nda) {
    $dossier->loadFromNDA($nda);
  }

  // patient + date (et pas de nda)
  if ($patient->_id && !$dossier->_id && $date) {
    $where = array();
    $where[]             = " '$date' BETWEEN entree AND sortie ";
    $where["patient_id"] = " = '$patient->_id'";

    $dossier->loadObject($where);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("mail_id",    $mail_id);
$smarty->assign("patient",    $patient);
$smarty->assign("dossier_id", $dossier->_id);

$smarty->display("inc_vw_attach_piece.tpl");
