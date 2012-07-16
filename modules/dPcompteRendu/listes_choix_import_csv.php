<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$owner_guid = CValue::get('owner_guid');
$file = isset($_FILES['import']) ? $_FILES['import'] : null;
$owner = null;

$owner = CMbObject::loadFromGuid($owner_guid);
if ($file && $owner && $owner->_id && ($fp = fopen($file['tmp_name'], 'r'))) {
  $user_id = $function_id = $group_id = null;
  
  switch($owner->_class) {
    case 'CMediusers': $user_id     = $owner->_id; break;
    case 'CFunctions': $function_id = $owner->_id; break;
    case 'CGroups':    $group_id    = $owner->_id; break;
  }
  
  // Object columns on the first line
  $cols = fgetcsv($fp);
  
  // Each line
  while($line = fgetcsv($fp)) {
    $object = new CListeChoix;
    foreach($cols as $index => $field) {
      $object->$field = $line[$index] === "" ? null : $line[$index];
    }
    
    $object->user_id     = $user_id;
    $object->function_id = $function_id;
    $object->group_id    = $group_id;
    $alreadyExists = $object->loadMatchingObjectEsc();
    
    if ($msg = $object->store()) {
      CAppUI::setMsg($msg);
    }
    else {
      if ($alreadyExists)
        CAppUI::setMsg("Liste de choix déjà présente");
      else
        CAppUI::setMsg("CAideSaisie-msg-create");
    }
  }
  fclose($fp);
  
  // Window refresh
  echo '<script type="text/javascript">window.opener.location.reload();</script>';
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("owner", $owner);
$smarty->assign("owner_guid", $owner_guid);
$smarty->display("listes_choix_import_csv.tpl");
