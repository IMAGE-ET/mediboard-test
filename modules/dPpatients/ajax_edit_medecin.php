<?php

/**
 * $Id$
 *
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$duplicate = CValue::get("duplicate", 0);

$medecin = new CMedecin();
$medecin->load(CValue::get("medecin_id"));

if ($duplicate) {
  $medecin->_id = null;
}

if (CAppUI::conf('dPpatients CPatient function_distinct') && $medecin->_id) {
  $current_user  = CMediusers::get();
  $is_admin      = $current_user->isAdmin();
  $same_function = $current_user->function_id == $medecin->function_id;
  if (!$is_admin && !$same_function) {
    CAppUI::redirect("m=system&a=access_denied");
  }
}

$medecin->loadSalutations();
$medecin->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("object", $medecin);
$smarty->display("inc_edit_medecin.tpl");