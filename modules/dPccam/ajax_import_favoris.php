<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();
ini_set("memory_limit", "2048M");
set_time_limit(1800);

$nomenclature = CValue::get("nomenclature", "ccam");

$file = isset($_FILES['import']) ? $_FILES['import'] : null;

$results = array();
$i = 0;

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  $cols = fgetcsv($fp, null, ";");
  
  while($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
    
    $results[$i]["praticien"] = trim($line[0]);
    $results[$i]["tag"]       = trim($line[1]);
    
    if ($nomenclature == "ccam") {
      $results[$i]['chapitres'] = trim($line[2]);
      $results[$i]["code"]      = array(trim($line[3]));
      if ($chaps = $results[$i]['chapitres']) {
        $ds = CSQLDataSource::get("ccamV2");
        $query = "SELECT CODE FROM actes WHERE 0 ";
        $chaps = explode(".", $chaps);
        
        if (count($chaps)) {
          $query .= "OR (";
          $query_chaps = array();
          foreach ($chaps as $key => $_chap) {
            $query_chaps[] = "ARBORESCENCE".($key+1)." = '" . str_pad($_chap, 6, "0", STR_PAD_LEFT) . "' ";
          }
          $query .= implode(" AND ", $query_chaps) . ")";
        }
        $list_codes = $ds->loadList($query);
       
        $results[$i]["code"] = CMbArray::pluck($list_codes, "CODE");
      }
      $results[$i]["object_class"] = trim($line[4]);
      
      switch ($results[$i]["object_class"]) {
        case "Séjour":
          $object_class = "CSejour";
          break;
        case "Consultation":
          $object_class = "CConsultation";
          break;
        case "Intervention":
        default:
          $object_class = "COperation";
      }
    }
    else {
      $results[$i]["code"]      = array(trim($line[2]));
    }
    
    $praticien = $results[$i]["praticien"];
    $results[$i]['error'] = 0;
    
    $last_space = strrpos($praticien, " ");
    
    $last_name = substr($praticien, 0, $last_space);
    $first_name = substr($praticien, $last_space + 1);
    
    $user = new CUser();
    $user->user_last_name = $last_name;
    $user->user_first_name = $first_name;
    $user->loadMatchingObjectEsc();
    
    if (!$user->_id) {
      $results[$i]['error'] = 'Utilisateur non trouvé';
      continue;
    }
    
    foreach ($results[$i]["code"] as $_code) {
      switch ($nomenclature) {
        case "ccam":
          $object = new CFavoriCCAM();
          $object->object_class = $object_class;
          break;
        case "cim10":
          $object = new CFavoriCIM10();
      }
      
      $object->favoris_user = $user->_id;
      $object->favoris_code = $_code;
      
      $object->loadMatchingObjectEsc();
      $msg = "";
      
      if (!$object->_id) {
        $msg = $object->store();
      
        if ($msg) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
          $results[$i]["error"] = $msg;
          $i++;
          continue;
        }
        
        CAppUI::setMsg("Favori créé", UI_MSG_OK);
      }
      
      $tag_name = $results[$i]["tag"];
      
      if ($tag_name) {
        $tag = new CTag();
        $tag->name = $tag_name;
        $tag->object_class = $object->_class;
        $tag->loadMatchingObjectEsc();
        
        if (!$tag->_id) {
          $msg = $tag->store();
          
          if ($msg) {
            CAppUI::setMsg($msg, UI_MSG_ERROR);
            $results[$i]["error"] = $msg;
            $i++;
            continue;
          }
        }
        $tag_item = new CTagItem();
        $tag_item->tag_id = $tag->_id;
        $tag_item->object_id = $object->_id;
        $tag_item->object_class = $object->_class;
        
        $tag_item->loadMatchingObjectEsc();
        if (!$tag_item->_id) {
          $msg = $tag_item->store();
          
          if ($msg) {
            CAppUI::setMsg($msg, UI_MSG_ERROR);
            $results[$i]["error"] = $msg;
            $i++;
            continue;
          }
        }
      }
    }
    $results[$i]["code"] = implode(" - ", $results[$i]["code"]);
    $i++;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

$smarty = new CSmartyDP();

$smarty->assign("nomenclature", $nomenclature);
$smarty->assign("results", $results);

$smarty->display("inc_import_favoris.tpl");
