<?php

/**
 * Import CSV des aides à la saisie
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$owner_guid = CValue::get("owner_guid");
$file = isset($_FILES["import"]) ? $_FILES["import"] : null;
$owner = null;

$owner = CMbObject::loadFromGuid($owner_guid);
if ($file && $owner && $owner->_id && ($fp = fopen($file["tmp_name"], "r"))) {
  $user_id = $function_id = $group_id = null;
  
  switch ($owner->_class) {
    case "CMediusers":
      $user_id = $owner->_id;
      break;
    case "CFunctions":
      $function_id = $owner->_id;
      break;
    case "CGroups":
      $group_id = $owner->_id;
  }
  
  // Object columns on the first line
  $cols = fgetcsv($fp);
  
  // Each line
  while ($line = fgetcsv($fp)) {
    $aide = new CAideSaisie;
    foreach ($cols as $index => $field) {
      $aide->$field = $line[$index] === "" ? null : $line[$index];
    }
    
    $aide->user_id     = $user_id;
    $aide->function_id = $function_id;
    $aide->group_id    = $group_id;
    
    $aide->escapeValues();
    $alreadyExists = $aide->loadMatchingObject();
    
    if ($msg = $aide->store()) {
      CAppUI::setMsg($msg);
      continue;
    }

    if ($alreadyExists) {
      CAppUI::setMsg("Aide à la saisie déjà présente");
    }
    else {
      CAppUI::setMsg("CAideSaisie-msg-create");
    }
  }
  fclose($fp);
  
  // Window refresh
  echo "<script type='text/javascript'>window.opener.location.reload();</script>";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("owner", $owner);
$smarty->assign("owner_guid", $owner_guid);

$smarty->display("aides_import_csv.tpl");
