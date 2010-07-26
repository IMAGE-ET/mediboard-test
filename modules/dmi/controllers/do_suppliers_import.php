<?php /* $Id: httpreq_do_sync_products.php 7207 2009-11-03 12:03:30Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: 7207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Recuperation du fichier
$file = CValue::read($_FILES, "datafile");

$first_line_label = "raison sociale";
$delim = ",";
$mode = "!one_line";

if (strtolower(CMbPath::getExtension($file["name"]) != 'csv')) 
  CAppUI::setMsg("Le fichier doit être de type CSV", UI_MSG_ERROR);

$csv = fopen($file["tmp_name"], 'r');

// On avance jusqu'a trouver la premiere ligne
while (($data = fgetcsv($csv, null, $delim)) !== false && (strtolower($data[0]) !== $first_line_label));
  
if ($mode == "one_line") {
    $map = array(
     0 => "name",
   //1 => Raison sociale du distributeur
     2 => "distributor_code",
   //3 => Nature,
     4 => "address",
     5 => "address",
     6 => "postal_code",
     7 => "city",
   //8 => Minimum de commande,
     9 => "carriage_paid",
    10 => "code",
    11 => "phone",
    12 => "fax",
    13 => "email",
    14 => "customer_code",
  //15 => Identifiant interne,
  //16 => Facturé par,
  //17 => Reglé par,
  //18 => Code interne,
  );
  
  $n = 10;
  while ((($data = fgetcsv($csv, null, $delim)) !== false)/* && $n--*/) {
    $data = array_map("trim", $data);
    
    $societe = new CSociete;
    $societe->name = $societe->_spec->ds->escape($data[0]);
    $societe->loadMatchingObject();
    
    $existed = $societe->_id;
    
    $prec_col = null;
    foreach($map as $from => $to) {
      // Concatenation
      if ($prec_col == $to) {
        $v = ($data[$from] ? "{$societe->$to}\n{$data[$from]}" : $societe->$to);
      }
      else {
        if (in_array($to, array("phone", "fax"))) {
          $data[$from] = preg_replace("/\\(\\d\\)/", "", $data[$from]);
          $data[$from] = preg_replace("/[\.-\\s\\(\\)\\/]/", "", $data[$from]);
          if (strlen($data[$from]) == 9) {
            $data[$from] = "0{$data[$from]}";
          }
        }
        $v = $data[$from];
      }
      $societe->$to = $v;
      
      $prec_col = $to;
    }
    
    $societe->repair();
    if ($msg = $societe->store()) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    }
    else {
      CAppUI::setMsg($existed ? "Société mise à jour" : "Société importée", UI_MSG_OK);
    }
  }
}

else {
  $current = array();
  
  $map = array(
    "name" => array(0, 0),
    "distributor_code" => array(2, 5), // name !!
    "address" => array(
      array(0, 1),
      array(6, 1),
    ),
    "postal_code" => array(0, 2),
    "city" => array(1, 2),
    "carriage_paid" => array(8, 3),
    "phone" => array(9, 0),
    "fax" => array(11, 0),
    "customer_code" => array(8, 4),
    "contact_name" => array(15, 0),
  );
  
  while ((($data = fgetcsv($csv, null, $delim)) !== false)/* && $n--*/) {
    $data = array_map("trim", $data);
    
    $end = ($data[0] == "Distribué par:");
    
    $current[] = $data;
    
    if ($end) {
      $coords = $map["name"];
      
      $societe = new CSociete;
      $societe->name = $societe->_spec->ds->escape($current[$coords[1]][$coords[0]]);
      $societe->loadMatchingObject();
      
      if (!$societe->_id) {
        $existed = $societe->_id;
        
        foreach($map as $field => $coords) {
          // Concatenation
          if (is_array($coords[0])) {
            $v = $current[$coords[0][1]][$coords[0][0]]."\n".$current[$coords[1][1]][$coords[1][0]];
          }
          else {
            if ($field === "distributor_code") {
              $_v = $current[$coords[1]][$coords[0]];
              if ($_v === "lui-même") $_v = null;
              else {
                
              }
              $current[$coords[1]][$coords[0]] = $_v;
            }
            elseif (in_array($field, array("phone", "fax"))) {
              $_v = $current[$coords[1]][$coords[0]];
              $_v = preg_replace("/\\(\\d\\)/", "", $_v);
              $_v = preg_replace("/[\.-\\s\\(\\)\\/]/", "", $_v);
              if (strlen($_v) == 9) {
                $v = "0{$_v}";
              }
              $current[$coords[1]][$coords[0]] = $_v;
            }
            
            $v = $current[$coords[1]][$coords[0]];
          }
          
          $societe->$field = $v;
        }
        
        $societe->repair();
        if ($msg = $societe->store()) {
          CAppUI::setMsg($msg, UI_MSG_WARNING);
        }
        else {
          CAppUI::setMsg($existed ? "Société mise à jour" : "Société importée", UI_MSG_OK);
        }
      }

      // next !
      $current = array();
    }
  }
}

fclose($csv);
