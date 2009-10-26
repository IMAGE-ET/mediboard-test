<?php /* $Id: cellSaver.class.php 6103 2009-04-16 13:36:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: 6103 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $AppUI;
$can->needsRead();

$owner_guid = mbGetValueFromGet('owner_guid');
$file = isset($_FILES['import']) ? $_FILES['import'] : null;
$owner = null;

$owner = CMbObject::loadFromGuid($owner_guid);
if ($file && $owner && $owner->_id && ($fp = fopen($file['tmp_name'], 'r'))) {
  $user_id = $function_id = $group_id = '';
  
  switch($owner->_class_name) {
    case 'CMediusers': $user_id = $owner->_id; break;
    case 'CFunctions': $function_id = $owner->_id; break;
    case 'CGroups':    $group_id = $owner->_id; break;
  }
  
  // Object columns on the first line
  $cols = fgetcsv($fp);
  
  // Each line
  while($line = fgetcsv($fp)) {
    $aide = new CAideSaisie;
    foreach($cols as $index => $field) {
      $aide->$field = $line[$index];
    }
    
    $aide->user_id     = $user_id;
    $aide->function_id = $function_id;
    $aide->group_id    = $group_id;
    
    if ($msg = $aide->store()) {
      CAppUI::setMsg($msg);
    }
    else {
      CAppUI::setMsg(CAppUI::tr("CAideSaisie-msg-create"));
    }
  }
  fclose($fp);
  
  // Window refresh
  echo '<script type="text/javascript">window.opener.location.href = window.opener.location.href;</script>';
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("owner", $owner);
$smarty->assign("owner_guid", $owner_guid);
$smarty->display("aides_import_csv.tpl");
